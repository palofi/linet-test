<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Contract;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Contract>
 */
final class ContractFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Contract::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'id' => self::faker()->numberBetween(1, 5000),
            'name' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
