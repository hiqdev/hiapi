<?php
declare(strict_types=1);

namespace hiapi\validators\Reflection;

use hiapi\commands\BaseCommand;
use hiapi\validators\IdValidator;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionObject;
use yii\validators\RegularExpressionValidator;
use yii\validators\Validator;

/**
 * Class ValidatorReflection inspects project Yii-based Validator and
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ValidatorReflection
{
    protected ContextFactory $contextFactory;
    protected TypeResolver   $typeResolver;
    protected ReflectionObject $reflection;
    protected Validator $validator;
    protected ?DocBlockFactory $docBlockFactory;

    /**
     * @psalm-param class-sting<Validator>
     */
    public static function fromClassname(string $className): self
    {
        return new self(new $className());
    }

    public function __construct(
        Validator $validator,
        DocBlockFactory $docBlockFactory = null,
        ContextFactory $contextFactory = null,
        TypeResolver $typeResolver = null
    ) {
        $this->validator = $validator;
        $this->reflection = new ReflectionObject($validator);

        $this->docBlockFactory = $docBlockFactory ?? DocBlockFactory::createInstance();
        $this->contextFactory = $contextFactory ?? new ContextFactory();
        $this->typeResolver = $typeResolver ?? new TypeResolver();

    }
    
    public function getPattern(): ?string 
    {
        if ($this->reflection->isSubclassOf(RegularExpressionValidator::class)) {
            return $this->reflection->getDefaultProperties()['pattern'];
        }

        return null;
    }

    public function getOpenApiType(): string
    {
        if ($this->validator instanceof IdValidator) { // TODO: somehow better
            return 'int';
        }

        return 'string';
    }

    public function getSummary(): string
    {
        $docBlock = $this->docBlockFactory->create($this->reflection);

        return $docBlock->getSummary();
    }

    public function getDescription(): string
    {
        $docBlock = $this->docBlockFactory->create($this->reflection);

        return $docBlock->getDescription()->__toString();
    }

    public function getExample(): ?string
    {
        $docBlock = $this->docBlockFactory->create($this->reflection);

        $examples = $docBlock->getTagsByName('example');
        if (empty($examples)) {
            return null;
        }

        return reset($examples)->__toString();
    }
}
