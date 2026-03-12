<?php
declare(strict_types=1);

namespace hiapi\commands\Reflection;

use hiapi\commands\BaseCommand;

final readonly class BaseCommandReflection
{
    /**
     * @psalm-param class-sting<BaseCommand>
     */
    public static function fromClassname(string $className): self
    {
        return new self(new $className());
    }

    public function __construct(private BaseCommand $command)
    {
    }

    /**
     * @psalm-return list<string>
     */
    public function getAttributes(): array
    {
        return array_keys($this->command->getAttributes());
    }

    /**
     * @return \yii\validators\Validator[]
     */
    public function getAttributeValidationRules(string $attribute): array
    {
        return $this->command->getActiveValidators($attribute);
    }
}
