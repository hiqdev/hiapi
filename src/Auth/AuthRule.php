<?php

declare(strict_types=1);
/**
 * Billing Adapter for MRDP database
 *
 * @link      https://github.com/hiqdev/billing-mrdp
 * @package   billing-mrdp
 * @license   proprietary
 * @copyright Copyright (c) 2020, HiQDev (http://hiqdev.com/)
 */

namespace hiapi\Core\Auth;

use hiqdev\DataMapper\Query\Specification;
use Webmozart\Assert\Assert;

/**
 * Class AuthRule
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @psalm-immutable
 */
final class AuthRule
{
    public ?string $client;
    public ?string $clientId;

    public bool $canSeeSellerObjects = false;

    public static function currentUser(): self
    {
        $self = new self();
        $self->client = null;
        $self->clientId = null;

        return $self;
    }

    public static function client(string $login): self
    {
        Assert::stringNotEmpty($login);

        $self = new self();
        $self->client = $login;
        $self->clientId = null;

        return $self;
    }

    public static function clientId(string $clientId): self
    {
        Assert::stringNotEmpty($clientId);

        $self = new self();
        $self->client = null;
        $self->clientId = $clientId;

        return $self;
    }

    public function canSeeSellerObjects(): self
    {
        $this->canSeeSellerObjects = true;

        return $this;
    }

    public function applyToSpecification(Specification $specification): Specification
    {
        $specification->where[self::class] = $this;

        return $specification;
    }
}
