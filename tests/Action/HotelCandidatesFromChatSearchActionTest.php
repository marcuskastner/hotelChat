<?php

namespace App\Tests\Action;

use App\Action\HotelCandidatesFromChatSearchAction;
use App\Entity\HotelCandidate;
use App\Entity\HotelSearch;
use App\Repository\HotelToChannelSourceRepository;
use App\Service\ContentServiceClient;
use App\Service\CoordinatePolygonService;
use InvalidArgumentException;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\MockAgent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class HotelCandidatesFromChatSearchActionTest extends KernelTestCase
{
    public function testGetHotelCandidatesReturnsMappedHotelsWithAresIds(): void
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
        $contentResponse = new JsonMockResponse([
            'result' => [
                ['property_id' => 1001],
                ['property_id' => 1002],
            ],
        ]);
        $httpClient = new MockHttpClient($contentResponse);
        $repository = $this->createMock(HotelToChannelSourceRepository::class);
        $repository->expects(self::once())
            ->method('decorateAresHotelIds')
            ->willReturnCallback(function (array $candidates): void {
                foreach ($candidates as $candidate) {
                    if ($candidate->expediaPropertyId === 1001) {
                        $candidate->setAresHotelId(42);
                    }
                }
            });

        $candidates = $this->action($agent, $httpClient, $repository)->getHotelCandidates($message);

        self::assertCount(1, $candidates);
        $candidate = $candidates[array_key_first($candidates)];
        self::assertInstanceOf(HotelCandidate::class, $candidate);
        self::assertSame(1001, $candidate->expediaPropertyId);
        self::assertSame(42, $candidate->getAresHotelId());
        $agent->assertCalledWith($message);
        $agent->assertCallCount(1);
        self::assertSame(1, $httpClient->getRequestsCount());
        self::assertSame('POST', $contentResponse->getRequestMethod());
        self::assertSame('http://localhost:8000/hotel/expedia/geography', $contentResponse->getRequestUrl());
        self::assertEquals(
            (new CoordinatePolygonService())->generate(new HotelSearch(
                city: 'Portland',
                state: 'OR',
                checkIn: '2026-09-15',
                checkOut: '2026-09-17',
                adults: 1,
                children: 0,
                rooms: 1,
                amenities: [],
                latitude: 45.5234515,
                longitude: -122.6762071,
            )),
            json_decode($contentResponse->getRequestOptions()['body'], true, flags: \JSON_THROW_ON_ERROR),
        );
    }

    public function testGetHotelCandidatesRejectsInvalidSearch(): void
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
        $repository = $this->createMock(HotelToChannelSourceRepository::class);
        $repository->expects(self::never())->method('decorateAresHotelIds');

        $this->expectException(InvalidArgumentException::class);

        $this->action($agent, $httpClient, $repository)->getHotelCandidates($message);
    }

    public function testGetHotelCandidatesRejectsUnknownCity(): void
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
        $repository = $this->createMock(HotelToChannelSourceRepository::class);
        $repository->expects(self::never())->method('decorateAresHotelIds');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Location not found for city: Nowhere, state: OR');

        $this->action($agent, $httpClient, $repository)->getHotelCandidates($message);
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

    private function action(
        MockAgent $agent,
        MockHttpClient $httpClient,
        HotelToChannelSourceRepository $repository,
    ): HotelCandidatesFromChatSearchAction {
        self::bootKernel();
        static::getContainer()->set(AgentInterface::class, $agent);
        static::getContainer()->set(
            ContentServiceClient::class,
            new ContentServiceClient($httpClient, 'http://localhost:8000'),
        );
        static::getContainer()->set(HotelToChannelSourceRepository::class, $repository);

        return static::getContainer()->get(HotelCandidatesFromChatSearchAction::class);
    }
}
