<?php
declare(strict_types=1);

namespace hiapi\Service\Customer;

use Ramsey\Uuid\UuidInterface;

class AccountClientIdResolver implements AccountClientIdResolverInterface
{
    public function resolveByAccountId(UuidInterface $accountId): ?int
    {
        return 0;
    }

    public function getAccountUuidByClientId(int $clientId): ?UuidInterface
    {
        return null;
    }

    public function getUserUuidByClientId(int $clientId): ?UuidInterface
    {
        return null;
    }
}
