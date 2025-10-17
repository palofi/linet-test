<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Customer;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Customer>
 */
final class CustomerFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Customer::class;
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
