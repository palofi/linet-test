<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Order;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Order>
 */
final class OrderFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Order::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'contract' => ContractFactory::new(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'customer' => CustomerFactory::new(),
            'customerOrderNumber' => self::faker()->text(255),
            'orderNumber' => self::faker()->text(20),
            'status' => StatusFactory::new(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
