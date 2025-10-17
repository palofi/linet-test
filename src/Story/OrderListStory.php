<?php

declare(strict_types=1);

namespace App\Story;

use App\Enum\StatusEnum;
use App\Factory\ContractFactory;
use App\Factory\CustomerFactory;
use App\Factory\OrderFactory;
use App\Factory\StatusFactory;
use Zenstruck\Foundry\Story;

final class OrderListStory extends Story
{
    public function build(): void
    {
        $activeStatus = StatusFactory::createOne([
            'id' => StatusEnum::ACTIVE,
            'name' => StatusEnum::ACTIVE->getLabel(),
            'userName' => 'test.user',
            'userFullName' => 'Test User',
        ]);

        $newStatus = StatusFactory::createOne([
            'id' => StatusEnum::NEW,
            'name' => StatusEnum::NEW->getLabel(),
            'userName' => 'admin.user',
            'userFullName' => 'Admin User',
        ]);

        $customer1 = CustomerFactory::createOne([
            'id' => 100,
            'name' => 'Test Customer 1',
        ]);

        $customer2 = CustomerFactory::createOne([
            'id' => 101,
            'name' => 'Test Customer 2',
        ]);

        $contract1 = ContractFactory::createOne([
            'id' => 200,
            'name' => 'test-contract-1',
        ]);

        $contract2 = ContractFactory::createOne([
            'id' => 201,
            'name' => 'test-contract-2',
        ]);

        OrderFactory::createOne([
            'id' => 1001,
            'orderNumber' => 'TEST-1001',
            'customerOrderNumber' => 'customer-order-1',
            'createdAt' => new \DateTimeImmutable('2024-01-15T10:00:00Z'),
            'requestedDeliveryAt' => new \DateTimeImmutable('2024-01-20T10:00:00Z'),
            'status' => $activeStatus,
            'customer' => $customer1,
            'contract' => $contract1,
        ]);

        OrderFactory::createOne([
            'id' => 1002,
            'orderNumber' => 'TEST-1002',
            'customerOrderNumber' => 'customer-order-2',
            'createdAt' => new \DateTimeImmutable('2024-02-15T10:00:00Z'),
            'requestedDeliveryAt' => new \DateTimeImmutable('2024-02-20T10:00:00Z'),
            'status' => $newStatus,
            'customer' => $customer2,
            'contract' => $contract2,
        ]);

        $this->addState('activeStatus', $activeStatus);
        $this->addState('newStatus', $newStatus);
        $this->addState('customer1', $customer1);
        $this->addState('customer2', $customer2);
        $this->addState('contract1', $contract1);
        $this->addState('contract2', $contract2);
    }
}
