# Event System

## Pattern: In-Memory Storage → Post-Command Emission

### Recording Events

Handlers use `EventAwareTrait` to record events during execution:

```php
$this->recordThat(new SomeEvent($entity));
```

Events accumulate in `$events[]` array on the handler instance.

### Emission Flow

1. Handler records events via `recordThat()`
2. Events are released to `EventStorage` (singleton) via `releaseEvents()`
3. `EventEmitterMiddleware` retrieves stored events AFTER command handler completes
4. Events emitted via League/Event `EmitterInterface`
5. Exceptions in listeners are caught and logged (not thrown)

### Key Behaviors

- Events release AFTER the command succeeds, not during
- If exception occurs before `EventEmitterMiddleware`, events are NOT emitted
- Event storage is per-request (not transactional)
- If DB transaction rolls back, events may still emit — handlers must be idempotent
- Events can be published to AMQP/Kafka via `PublishToQueueListener` / `PublishToExchangeListener`
- Listeners are configured in `ConfigurableEmitter` with event name patterns
