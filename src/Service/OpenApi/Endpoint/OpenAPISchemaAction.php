<?php
declare(strict_types=1);

namespace hiapi\Service\OpenApi\Endpoint;

use cebe\openapi\Writer;
use hiapi\Service\OpenApi\OpenAPIGenerator;
use Laminas\Diactoros\Response;

final class OpenAPISchemaAction
{
    private OpenAPIGenerator $openApiGenerator;

    public function __construct(OpenAPIGenerator $openApiGenerator)
    {
        $this->openApiGenerator = $openApiGenerator;
    }

    public function __invoke(OpenAPISchemaCommand $command)
    {
        $openApi = $this->openApiGenerator->__invoke();
        $json = Writer::writeToJson($openApi);
        return new Response\TextResponse($json, 200, [
            'Content-Type' => ['application/json'],
        ]);
    }
}
