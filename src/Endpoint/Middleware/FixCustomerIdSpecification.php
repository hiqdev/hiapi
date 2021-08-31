<?php
declare(strict_types=1);

namespace hiapi\Core\Endpoint\Middleware;

use hiapi\commands\SearchCommand;
use hiapi\Service\Customer\AccountClientIdResolverInterface;
use League\Tactician\Middleware;
use Ramsey\Uuid\Uuid;

final class FixCustomerIdSpecification implements Middleware
{
    private AccountClientIdResolverInterface $accountClientIdResolver;

    public function __construct(AccountClientIdResolverInterface $accountClientIdResolver)
    {
        $this->accountClientIdResolver = $accountClientIdResolver;
    }

    /**
     * @param SearchCommand $command
     */
    public function execute($command, callable $next)
    {
        $possibleFilterNames = ['customer_id', 'customer-id'];
        foreach ($possibleFilterNames as $key) {
            if (isset($command->where[$key])) {
                $command->where[$key] = $this->fixCustomerId($command->where[$key]);
            }
        }

        if (isset($command->customer_id)) {
            $command->customer_id = $this->fixCustomerId($command->customer_id);
        }

        return $next($command);
    }

    private function fixCustomerId($customer_id)
    {
        return Uuid::isValid($customer_id)
            ? $this->accountClientIdResolver->resolveByAccountId(Uuid::fromString($customer_id))
            : $customer_id;
    }
}
