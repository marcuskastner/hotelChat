<?php

namespace App\Tests\Action;

use App\Action\HotelSearchFromChatAction;
use App\Entity\HotelSearch;
use InvalidArgumentException;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\MockAgent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HotelSearchFromChatActionTest extends KernelTestCase
{
    public function testGetHotelSearchParsesAndValidatesTheMessage(): void
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
            'amenities' => ['pool'],
        ]);

        $search = $this->action($agent)->getHotelSearch($message);

        self::assertInstanceOf(HotelSearch::class, $search);
        self::assertSame('Portland', $search->city);
        self::assertSame('OR', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(1, $search->adults);
        self::assertSame(0, $search->children);
        self::assertSame(1, $search->rooms);
        self::assertSame(['pool'], $search->amenities);
        $agent->assertCalledWith($message);
        $agent->assertCallCount(1);
    }

    public function testGetHotelSearchRejectsInvalidSearch(): void
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

        $this->expectException(InvalidArgumentException::class);

        $this->action($agent)->getHotelSearch($message);
    }

    public function testGetHotelSearchUsesFixtureForTestMessage(): void
    {
        $agent = new MockAgent([
            'test' => json_encode(['city' => 'ShouldNotBeUsed'], \JSON_THROW_ON_ERROR),
        ]);

        $search = $this->action($agent)->getHotelSearch('test');

        self::assertSame('San Diego', $search->city);
        self::assertSame('CA', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(2, $search->adults);
        self::assertSame(1, $search->rooms);
        self::assertSame(['pool'], $search->amenities);
        $agent->assertCallCount(0);
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

    private function action(MockAgent $agent): HotelSearchFromChatAction
    {
        self::bootKernel();
        static::getContainer()->set(AgentInterface::class, $agent);

        return static::getContainer()->get(HotelSearchFromChatAction::class);
    }
}
