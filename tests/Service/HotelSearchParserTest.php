<?php

namespace App\Tests\Service;

use App\Mapper\HotelSearchMapper;
use App\Service\HotelSearchParser;
use App\Service\OpenAiService;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Agent\MockAgent;

class HotelSearchParserTest extends TestCase
{
    public function testParseMapsAgentPayloadOntoHotelSearch(): void
    {
        $message = 'Find me a hotel in Portland';
        $agent = new MockAgent([
            $message => json_encode([
                'city' => 'Portland',
                'state' => 'OR',
                'checkIn' => '2026-09-15',
                'checkOut' => '2026-09-17',
                'adults' => 2,
                'children' => 1,
                'rooms' => 1,
                'amenities' => ['pool'],
            ], \JSON_THROW_ON_ERROR),
        ]);

        $parser = new HotelSearchParser(new OpenAiService($agent), new HotelSearchMapper());
        $search = $parser->parse($message);

        self::assertSame('Portland', $search->city);
        self::assertSame('OR', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(2, $search->adults);
        self::assertSame(1, $search->children);
        self::assertSame(1, $search->rooms);
        self::assertSame(['pool'], $search->amenities);
        self::assertNull($search->latitude);
        self::assertNull($search->longitude);
        $agent->assertCalledWith($message);
    }
}
