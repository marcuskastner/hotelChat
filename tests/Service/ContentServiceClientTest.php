<?php

namespace App\Tests\Service;

use App\Service\ContentServiceClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class ContentServiceClientTest extends TestCase
{
    public function testGetExpediaPropertiesPostsGeographyPayload(): void
    {
        $response = new JsonMockResponse(['properties' => [['id' => 'exp-1']]]);
        $client = new ContentServiceClient(
            new MockHttpClient($response),
            'http://localhost:8000',
        );

        $payload = [
            'coordinates' => [[
                [-122.6762071, 45.5234515],
                [-122.6762071, 45.5234515],
            ]],
        ];

        $result = $client->getExpediaProperties($payload);

        self::assertSame(['properties' => [['id' => 'exp-1']]], $result);
        self::assertSame('POST', $response->getRequestMethod());
        self::assertSame('http://localhost:8000/hotel/expedia/geography', $response->getRequestUrl());
        self::assertSame(
            $payload,
            json_decode($response->getRequestOptions()['body'], true, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testGetExpediaPropertiesDefaultsToEmptyPayload(): void
    {
        $response = new JsonMockResponse([]);
        $client = new ContentServiceClient(
            new MockHttpClient($response),
            'http://localhost:8000',
        );

        self::assertSame([], $client->getExpediaProperties());
        self::assertSame(
            [],
            json_decode($response->getRequestOptions()['body'], true, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testGetExpediaPropertiesThrowsOnServerError(): void
    {
        $client = new ContentServiceClient(
            new MockHttpClient(new JsonMockResponse(['error' => 'unavailable'], ['http_code' => 500])),
            'http://localhost:8000',
        );

        $this->expectException(ServerExceptionInterface::class);

        $client->getExpediaProperties(['coordinates' => []]);
    }
}
