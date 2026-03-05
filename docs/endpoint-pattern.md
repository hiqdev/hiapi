# Endpoint Pattern

## Overview

Endpoints are declarative, immutable specifications that define API operations.
They are built using a fluent builder with trait-based composition.

## Endpoint Lifecycle

1. Handler class implements `ActionInterface` with an `__invoke()` method
2. Handler is registered in `EndpointRepository` by class name (not instance)
3. On request, handler is called with `BuilderFactory` to construct the endpoint
4. `EndpointBuilder` accumulates configuration via traits:
   - `PermissionAwareBuilderTrait`
   - `NamedEndpointBuilderTrait`
   - `InOutParametersBuilderTrait`
5. Builder produces immutable `Endpoint` object
6. `EndpointProcessor` executes the command through the Tactician middleware chain

## Input/Output Types

Types can be either a class string or a `Collection` (for bulk operations):

- `BuilderFactory::many(ClassName)` creates a `Collection` value object
- When input is Collection, the command must implement `IteratorAggregate`
- `RepeatHandler` middleware maps a handler across each item
- `ReduceHandler` middleware wraps single commands into bulk

## Type Checking

TypeCheckMiddleware validates BOTH input and output with strict type matching.
Mismatched types throw RuntimeException.

## Commands

Commands extend `yii\base\Model` (Yii2 validation, attribute loading):

- `BaseCommand` — basic command with `rules()` and `validate()`
- `SearchCommand` — adds `where`, `filter`, `select`, `with`, pagination (default limit: 25)
- `BulkCommand` — wraps `ArrayCollection`, validates all items

Commands are loaded via `CommandFactory::createByEndpoint($endpoint, $request)`:
extracts data from parsed body + query params, calls `load($data, '')`.
