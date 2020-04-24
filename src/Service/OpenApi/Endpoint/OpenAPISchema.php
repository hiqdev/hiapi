<?php
declare(strict_types=1);

namespace hiapi\Service\OpenApi\Endpoint;

use hiapi\Core\Endpoint\BuilderFactory;
use hiapi\Core\Endpoint\Endpoint;
use hiapi\Core\Endpoint\EndpointBuilder;
use hiapi\endpoints\Module\Multitenant\Tenant;
use Laminas\Diactoros\Response;

final class OpenAPISchema
{
    public function __invoke(BuilderFactory $build): Endpoint
    {
        return $this->create($build)->build();
    }

    public function create(BuilderFactory $build): EndpointBuilder
    {
        return $build->endpoint(self::class)
            ->description('Builds OpenAPI schema')
            ->exportTo(Tenant::ALL)
            ->take(OpenAPISchemaCommand::class)
            ->middlewares(
                $build->call(OpenAPISchemaAction::class)
            )
            ->return(Response::class);
    }
}

