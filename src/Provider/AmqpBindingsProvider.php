<?php
declare(strict_types=1);

namespace hiapi\Provider;

use PhpAmqpLib\Channel\AMQPChannel;

/**
 * Class AmqpBindingsProvider
 * Usage:
 *
 * 1. Add your binding functions to {@see bindings}
 * 2. Call {@see bind()} when AMQP is initialized to create required queues and bindings for them.
 *
 * Example:
 * ```
 *  $connection = new \PhpAmqpLib\Connection\AMQPLazyConnection(
 *      $params['amqp.host'],
 *      $params['amqp.port'],
 *      $params['amqp.user'],
 *      $params['amqp.password']
 *  );
 *
 *  $bindings = $container->get(\hiapi\Provider\AmqpBindingsProvider::class);
 *  $bindings->bind($connection->channel());
 * ```
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class AmqpBindingsProvider
{
    /**
     * @var \Closure[] array of closures, that bind routing keys to a right queue.
     * Expected closure signature is:
     *
     * ```php
     * function (PhpAmqpLib\Channel\AMQPChannel $channel): void {
     *    // Bind here, e.g.
     *    $channel->queue_declare('recon.queue', false, true, false, false);
     *    $channel->exchange_declare('dbms.updates', 'topic', false, true, true, false, false);
     *    $channel->queue_bind('recon.queue', 'dbms.updates', 'bot.dns.*');
     * }
     * ```
     */
    public $bindings = [];

    public function bind(AMQPChannel $channel): void
    {
        foreach ($this->bindings as $bindTo) {
            $bindTo($channel);
        }
    }
}
