<?php

namespace App\Tests\Action;

use App\Action\HotelChatSearchAction;
use App\Service\ContentServiceClient;
use App\Service\CoordinatePolygonService;
use InvalidArgumentException;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\MockAgent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class HotelChatSearchActionTest extends KernelTestCase
{
    public function testExecuteReturnsSearchWithResolvedLocation(): void
    {
        $message = 'Find me a hotel in Portland';
        $agent = $this->mockAgent($message, [
            'city' => 'Portland',
            'state' => 'OR',
            'checkIn' => '2026-09-15',
            'checkOut' => '2026-09-17',
            'adults' => 1,
            'children' => 0,
            'rooms' => 1,
            'amenities' => [],
        ]);
        $contentResponse = new JsonMockResponse(['properties' => []]);
        $httpClient = new MockHttpClient($contentResponse);

        $search = $this->action($agent, $httpClient)->execute($message);

        self::assertSame('Portland', $search->city);
        self::assertSame('OR', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(45.5234515, $search->latitude);
        self::assertSame(-122.6762071, $search->longitude);
        $agent->assertCalledWith($message);
        $agent->assertCallCount(1);
        self::assertSame(1, $httpClient->getRequestsCount());
        self::assertSame('POST', $contentResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/hotel/expedia/geography', $contentResponse->getRequestUrl());
        self::assertEquals(
            (new CoordinatePolygonService())->generate($search),
            json_decode($contentResponse->getRequestOptions()['body'], true, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testExecuteRejectsInvalidSearch(): void
    {
        $message = 'Find me a hotel somewhere';
        $agent = $this->mockAgent($message, [
            'city' => null,
            'state' => 'OR',
            'checkIn' => '2026-09-15',
            'checkOut' => '2026-09-17',
            'adults' => 1,
            'children' => 0,
            'rooms' => 1,
            'amenities' => [],
        ]);
        $httpClient = $this->unusedHttpClient();

        $this->expectException(InvalidArgumentException::class);

        $this->action($agent, $httpClient)->execute($message);
    }

    public function testExecuteRejectsUnknownCity(): void
    {
        $message = 'Find me a hotel in Nowhere';
        $agent = $this->mockAgent($message, [
            'city' => 'Nowhere',
            'state' => 'OR',
            'checkIn' => '2026-09-15',
            'checkOut' => '2026-09-17',
            'adults' => 1,
            'children' => 0,
            'rooms' => 1,
            'amenities' => [],
        ]);
        $httpClient = $this->unusedHttpClient();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Location not found for city: Nowhere, state: OR');

        $this->action($agent, $httpClient)->execute($message);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function mockAgent(string $message, array $payload): MockAgent
    {
        return new MockAgent([
            $message => json_encode($payload, \JSON_THROW_ON_ERROR),
        ]);
    }

    private function unusedHttpClient(): MockHttpClient
    {
        return new MockHttpClient(function (): never {
            $this->fail('Content service should not have been called.');
        });
    }

    private function action(MockAgent $agent, MockHttpClient $httpClient): HotelChatSearchAction
    {
        self::bootKernel();
        static::getContainer()->set(AgentInterface::class, $agent);
        static::getContainer()->set(
            ContentServiceClient::class,
            new ContentServiceClient($httpClient, 'http://localhost:8000'),
        );

        return static::getContainer()->get(HotelChatSearchAction::class);
    }
}
