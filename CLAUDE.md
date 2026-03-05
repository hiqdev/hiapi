# Agent Instructions — hiapi

Base API framework. Yii2 extension.
Declares config-plugin groups: constants, params, common, web, console, tests.

@docs/request-flow.md
@docs/endpoint-pattern.md
@docs/event-system.md

## Key patterns

Command bus dispatches commands to handlers. Endpoints define API operations.
Middleware stack processes requests (auth, validation, error handling, JSON:API).
Two independent middleware chains exist: PSR-15 HTTP (outer) and Tactician command (inner).

## Rules

Do not add business logic here — this is infrastructure only.
New middleware must be registered in the web config.
JSON:API serialization is handled by JsonApiMiddleware — do not serialize manually in handlers.
