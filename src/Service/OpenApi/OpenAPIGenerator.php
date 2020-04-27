<?php
declare(strict_types=1);

namespace hiapi\Service\OpenApi;

use cebe\openapi\spec\Components;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Operation;
use cebe\openapi\spec\PathItem;
use cebe\openapi\spec\Response;
use cebe\openapi\spec\Responses;
use cebe\openapi\spec\Schema;
use cebe\openapi\spec\SecurityRequirement;
use cebe\openapi\spec\SecurityScheme;
use cebe\openapi\spec\Server;
use cebe\openapi\spec\ServerVariable;
use Generator;
use hiapi\commands\reflection\BaseCommandReflection;
use hiapi\Core\Endpoint\EndpointRepository;
use hiapi\endpoints\Module\InOutControl\VO\Collection;
use hiapi\exceptions\ConfigurationException;
use hiapi\legacy\lib\deps\arr;
use hiapi\validators\Reflection\ValidatorReflection;
use yii\validators\EachValidator;
use yii\validators\InlineValidator;
use yii\validators\RequiredValidator;
use yii\validators\SafeValidator;
use yii\validators\Validator;
use ReflectionObject;

/**
 * Class OpenAPIGenerator generates OpenAPI documentation.
 *
 * This class represents a general implementation.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class OpenAPIGenerator
{
    /** @psalm-var array<string, SecurityScheme> */
    private array $securitySchemes;
    private EndpointRepository $endpointRepository;
    /** @psalm-var list<string> */
    private array $hosts;
    private array $apiInfo;

    public function __construct(EndpointRepository $endpointRepository, array $options = [])
    {
        $this->endpointRepository = $endpointRepository;

        $this->hosts = $options['hosts'] ?? [];
        $this->securitySchemes = $options['securitySchemes'] ?? [];
        $this->apiInfo = $options['apiInfo'] ?? [];
    }

    public function __invoke(): OpenApi
    {
        return $this->createOpenAPI();
    }

    private function createOpenAPI(): OpenApi
    {
        return new OpenApi([
            'openapi' => '3.0.2',
            'info' => $this->apiInfo,
            'paths' => iterator_to_array($this->generatePaths(), true),
            'servers' => iterator_to_array($this->buildServers(), false),
            'components' => $this->buildComponents(),
        ]);
    }

    /**
     * @psalm-return Generator<int, Server>
     */
    private function buildServers(): Generator
    {
        $isDev = YII_ENV_DEV;

        foreach($this->hosts as $host) {
            yield new Server([
                'url' => "{protocol}://$host/",
                'variables' => [
                    'protocol' => new ServerVariable([
                        'enum' => ['http', 'https'],
                        'default' => $isDev ? 'http' : 'https',
                    ]),
                ],
            ]);
        }
    }

    /**
     * @psalm-return Generator<int, PathItem>
     */
    private function generatePaths(): Generator
    {
        foreach ($this->endpointRepository->getEndpointNames() as $name) {
            try {
                $endpoint = $this->endpointRepository->getByName($name);
                $input = $endpoint->inputType;

                yield "/$name" => new PathItem(array_filter([
                    'post' => new Operation([
                        'tags' => [], // TODO: parse endpoint name
                        'summary' => $endpoint->description,
                        'security' => iterator_to_array($this->generateSecuritySchemas($endpoint), true),
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/x-www-form-urlencoded' => [
                                    'schema' => $this->generateRequestSchema($endpoint),
                                ],
                            ],
                        ],
                        'responses' => new Responses([
                            'default' => new Response([
                                'description' => '',
                                // TODO: response example
                            ]),
                        ]),
                    ]),
                ]));
            } catch (ConfigurationException $exception) {
            }
        }
    }

    private function buildComponents(): Components
    {
        return new Components([
            'schemas' => $this->validationSchemas(),
            'securitySchemes' => $this->securitySchemes,
        ]);
    }

    /**
     * @return Schema[]
     */
    private function validationSchemas(): array
    {
        $result = [];
        foreach ($this->metValidators as $name => $className) {
            $reflection = ValidatorReflection::fromClassname($className);

            $result[$name] = new Schema(array_filter([
                'pattern' => $reflection->getPattern(),
                'type' => 'string',
                'description' => $reflection->getSummary(),
                'example' => $reflection->getExample(),
            ]));
        }

        return $result;
    }

    /**
     * @param \hiapi\Core\Endpoint\Endpoint $endpoint
     * @psalm-return Generator<int, SecurityScheme>
     */
    private function generateSecuritySchemas(\hiapi\Core\Endpoint\Endpoint $endpoint): Generator
    {
        if (!empty($endpoint->permissions)) {
            foreach ($this->securitySchemes as $name => $_) {
                yield new SecurityRequirement([
                    $name => $endpoint->permissions,
                ]);
            }
        }
    }

    private function generateRequestSchema(\hiapi\Core\Endpoint\Endpoint $endpoint): Schema
    {
        $properties = $required = [];

        $inputType = $endpoint->inputType;
        if ($inputType instanceof Collection) {
            // TODO: describe as a nested object
            $inputType = $inputType->getEntriesClass();
        }
        /** @psalm-var class-string<BaseCommand> $inputType */
        $reflection = BaseCommandReflection::fromClassname($inputType);

        foreach ($reflection->getAttributes() as $attribute) {
            $rules = $reflection->getAttributeValidationRules($attribute);

            $schemas = [];
            foreach ($rules as $key => $validator) {
                if ($validator instanceof RequiredValidator) {
                    $required[] = $attribute;
                }
                if (($rule = $this->ruleNameByValidator($validator)) === null) {
                    continue;
                }

                $schemas[$key] = new Schema([
                    '$ref' => '#/components/schemas/' . $rule,
                ]);
            }
            if (count($schemas) === 0) {
                continue; // Not validated attributes are not a part of a public API
            }

            if (count($schemas) === 1) {
                $properties[$attribute] = reset($schemas);
            } else {
                $properties[$attribute] = new Schema(['allOf' => $schemas]);
            }
        }

        return new Schema([
            'properties' => $properties,
            'required' => array_unique($required),
        ]);
    }

    /** @psalm-var array<string, class-string<Validator>> */
    private array $metValidators = [];

    private function ruleNameByValidator(Validator $validator): ?string
    {
        $skippedValidators = [
            RequiredValidator::class => 'Is represented by OpenAPI required syntax',
            EachValidator::class => 'Is represented by OpenAPI array syntax',
            InlineValidator::class => 'Could not be inspected and should not be used',
            SafeValidator::class => 'Means nothing in OpenAPI',
        ];
        if (isset($skippedValidators[get_class($validator)])) {
            return null;
        }

        $name = (new ReflectionObject($validator))->getShortName();
        if (!isset($this->metValidators[$name])) {
            $this->metValidators[$name] = get_class($validator);
        }

        return $name;
    }
}
