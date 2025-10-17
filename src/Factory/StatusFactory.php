<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Status;
use App\Enum\StatusEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Status>
 */
final class StatusFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Status::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            ...$this->randomIdAndName(),
            'createdAt' => self::faker()->dateTime(),
            'userName' => self::faker()->userName(),
            'userFullName' => self::faker()->name(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }

    /**
     * @return array{id: string, name: string}
     */
    private function randomIdAndName(): array
    {
        $enum = self::faker()->randomElement(StatusEnum::cases());
        return [
            'id' => $enum->value,
            'name' => $enum->getLabel(),
        ];
    }
}
