<?php

declare(strict_types=1);

namespace App\Tests\_dataProviders;

class OrderListDataProvider
{
    public static function provideValidFilters(): iterable
    {
        yield 'filter by active status' => [
            'filters' => ['status.id' => 'ACT'],
            'expectedMinCount' => 1,
            'description' => 'Should return orders with ACTIVE status'
        ];

        yield 'filter by new status' => [
            'filters' => ['status.id' => 'NEW'],
            'expectedMinCount' => 0,
            'description' => 'Should return orders with NEW status'
        ];

        yield 'filter by created date after' => [
            'filters' => ['createdAt[after]' => '2020-01-01'],
            'expectedMinCount' => 0,
            'description' => 'Should return orders created after 2020-01-01'
        ];

        yield 'filter by created date before' => [
            'filters' => ['createdAt[before]' => '2025-01-01'],
            'expectedMinCount' => 1,
            'description' => 'Should return orders created before 2025-01-01'
        ];
    }

    public static function provideEmptyResultFilters(): iterable
    {
        yield 'filter by closed status with no closed orders' => [
            'filters' => ['status.id' => 'END'],
            'description' => 'Should return empty array when no closed orders exist'
        ];

        yield 'filter by future date' => [
            'filters' => ['createdAt[after]' => '2030-01-01'],
            'description' => 'Should return empty array for future dates'
        ];

        yield 'filter by very old date' => [
            'filters' => ['createdAt[before]' => '2000-01-01'],
            'description' => 'Should return empty array for very old dates'
        ];
    }

    public static function provideInvalidFilters(): iterable
    {
        yield 'invalid status value' => [
            'filters' => ['status.id' => 'INVALID_STATUS'],
            'description' => 'Should handle invalid status enum value'
        ];

        yield 'invalid date format' => [
            'filters' => ['createdAt[after]' => 'invalid-date'],
            'description' => 'Should handle invalid date format'
        ];

        yield 'invalid filter parameter' => [
            'filters' => ['nonExistentField' => 'value'],
            'description' => 'Should handle non-existent filter fields'
        ];
    }
}
