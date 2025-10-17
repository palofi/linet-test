<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Story\OrderListStory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderItemTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetOrderItem(): void
    {
        OrderListStory::load();

        $response = static::createClient()->request('GET', '/api/order/1001', [
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        // TODO: Uncomment when API Platform fixes SerializedName issue in JSON Schema validation
        // See: https://github.com/api-platform/core/issues/4529 and https://github.com/api-platform/core/issues/4752
        // self::assertMatchesResourceItemJsonSchema(Order::class);

        $responseData = $response->toArray();
        self::assertSame(1001, $responseData['id']);
        self::assertSame('TEST-1001', $responseData['orderNumber']);
    }

    public function testGetOrderItemNotFound(): void
    {
        static::createClient()->request('GET', '/api/order/99999', [
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
