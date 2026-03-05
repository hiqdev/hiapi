# Request Flow

## Two Independent Middleware Chains

The framework has two separate middleware chains. They are independent —
a middleware in one cannot access the other.

### Chain A: PSR-15 HTTP Middleware (outer)

Processes the HTTP request/response:

```
1. QuietMiddleware          — checks if endpoint exists
2. CorsMiddleware
3. RequestBodyParser        — parses JSON/form body
4. ContentTypeMiddleware    — checks Accept/Content-Type headers
5. ExceptionMiddleware      — catches exceptions, formats HTTP error responses
6. BlacklistMiddleware      — CIDR-based IP filtering
7. UserRealIpMiddleware     — extracts real IP from X-Forwarded-For
8. EmptyEndpointMiddleware
9. AuthMiddleware (OAuth2)  — extracts bearer token, calls /userinfo, populates Yii2 $user
10. RouterMiddleware        — matches route, adds params to query
11. EndpointMiddleware      → enters inner chain
```

Configured in `config/request-handling.php`. Order is hard-coded in RequestHandler constructor.

### Chain B: Tactician Command Middleware (inner)

Processes the command inside EndpointProcessor:

```
1. TypeCheckMiddleware           — validates input/output types (strict, checks BOTH)
2. CheckPermissionsMiddleware    — $user->can($permission) via Yii2 RBAC
3. FixCustomerIdSpecification   — resolves customer ID aliases
4. ValidateCommandMiddleware     — $command->validate()
5. Custom endpoint middlewares   — from Endpoint definition
6. Command handler              — actual business logic
```

## Key Implications

- **Auth happens in HTTP layer** (step 9), **permissions check in command layer** (step 2 inner).
- **Events** are stored during handler execution, released AFTER handler succeeds
  via EventEmitterMiddleware. If exception occurs before release, events don't emit.
- **JsonApiMiddleware** wraps the command result. Never serialize manually in handlers.
- **Exception handling splits**: HTTP exceptions → ExceptionMiddleware (HTTP 4xx/5xx);
  command exceptions → HandleExceptionsMiddleware → CommandError objects → serialized by JsonApi.
- `ENABLE_JSONAPI_RESPONSE` env var switches between JsonApiMiddleware and LegacyResponderMiddleware.

## Authentication Detail

UserIdentity does NOT use database lookup. All user info comes from OAuth2 token response.
Standard Yii2 `findIdentity()` / `findIdentityByAccessToken()` throw `InvalidCallException`.
Identity is created via `UserIdentity::fromArray($oauthResponse)`.
