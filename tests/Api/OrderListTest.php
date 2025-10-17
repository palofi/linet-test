<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\_dataProviders\OrderListDataProvider;
use App\Story\OrderListStory;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class OrderListTest extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    public function testGetOrderList(): void
    {
        OrderListStory::load();
        $response = self::createClient()->request('GET', '/api/order/list', [
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $responseData = $response->toArray();

        self::assertIsArray($responseData);
        self::assertGreaterThanOrEqual(1, count($responseData));
        // TODO: Uncomment when API Platform fixes SerializedName issue in JSON Schema validation
        // See: https://github.com/api-platform/core/issues/4529 and https://github.com/api-platform/core/issues/4752
        // self::assertMatchesResourceCollectionJsonSchema(Order::class);

    }

    #[DataProviderExternal(OrderListDataProvider::class, 'provideValidFilters')]
    public function testGetOrderListWithValidFilters(array $filters, int $expectedMinCount, string $description): void
    {
        OrderListStory::load();
        $response = static::createClient()->request('GET', '/api/order/list', [
            'query' => $filters,
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $responseData = $response->toArray();
        self::assertIsArray($responseData);
        self::assertGreaterThanOrEqual($expectedMinCount, count($responseData), $description);
    }

    #[DataProviderExternal(OrderListDataProvider::class, 'provideEmptyResultFilters')]
    public function testGetOrderListWithEmptyResult(array $filters, string $description): void
    {

        $response = static::createClient()->request('GET', '/api/order/list' , [
            'query' => $filters,
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $responseData = $response->toArray();
        self::assertIsArray($responseData);
        self::assertCount(0, $responseData, $description);
    }

    #[DataProviderExternal(OrderListDataProvider::class, 'provideInvalidFilters')]
    public function testGetOrderListWithInvalidFilters(array $filters, string $description): void
    {
        $response = static::createClient()->request('GET', '/api/order/list' , [
            'query' => $filters,
            'headers' => ['Accept' => 'application/json']
        ]);

        self::assertThat(
           $response->getStatusCode(),
            $this->logicalOr(
                $this->equalTo(Response::HTTP_OK),
                $this->equalTo(Response::HTTP_BAD_REQUEST)
            ),
            $description
        );
    }

}
