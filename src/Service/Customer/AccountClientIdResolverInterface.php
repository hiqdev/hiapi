<?php
declare(strict_types=1);

namespace hiapi\Service\Customer;

use Ramsey\Uuid\UuidInterface;

interface AccountClientIdResolverInterface
{
    public function resolveByAccountId(UuidInterface $accountId): ?int;
}
