<?php

namespace App\Tests\Service;

use App\Mapper\HotelSearchMapper;
use App\Service\HotelSearchParser;
use App\Service\OpenAiService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\AI\Agent\MockAgent;

class HotelSearchParserTest extends TestCase
{
    public function testParseMapsAgentPayloadOntoHotelSearch(): void
    {
        $today = new DateTimeImmutable('2026-09-16');
        $message = 'Find me a hotel in Portland';
        $prefixed = (new OpenAiService(new MockAgent(), $today))->prefixedMessage($message);
        $agent = new MockAgent([
            $prefixed => json_encode([
                'city' => 'Portland',
                'state' => 'OR',
                'checkIn' => '2026-09-19',
                'checkOut' => '2026-09-20',
                'adults' => 2,
                'children' => 1,
                'rooms' => 1,
                'amenities' => ['swimming_pool'],
                'rankingWeights' => [
                    'rating' => 'HIGH',
                    'price' => 'NA',
                    'location' => 'NA',
                    'amenity' => 'HIGH',
                ],
            ], \JSON_THROW_ON_ERROR),
        ]);

        $parser = new HotelSearchParser(new OpenAiService($agent, $today), new HotelSearchMapper());
        $search = $parser->parse($message);

        self::assertSame('Portland', $search->city);
        self::assertSame('OR', $search->state);
        self::assertSame('2026-09-19', $search->checkIn);
        self::assertSame('2026-09-20', $search->checkOut);
        self::assertSame(2, $search->adults);
        self::assertSame(1, $search->children);
        self::assertSame(1, $search->rooms);
        self::assertSame(['swimming_pool'], $search->amenities);
        self::assertSame([
            'rating' => 'HIGH',
            'price' => 'NA',
            'location' => 'NA',
            'amenity' => 'HIGH',
        ], $search->rankingWeights);
        self::assertNull($search->latitude);
        self::assertNull($search->longitude);
        $agent->assertCalledWith($prefixed);
    }

    public function testParseUsesDefaultRankingWeightsWhenOmitted(): void
    {
        $today = new DateTimeImmutable('2026-09-16');
        $message = 'Find me a hotel in San Diego';
        $prefixed = (new OpenAiService(new MockAgent(), $today))->prefixedMessage($message);
        $agent = new MockAgent([
            $prefixed => json_encode([
                'city' => 'San Diego',
                'state' => 'CA',
                'checkIn' => '2026-09-15',
                'checkOut' => '2026-09-17',
                'amenities' => ['swimming_pool', 'pets_allowed'],
            ], \JSON_THROW_ON_ERROR),
        ]);

        $parser = new HotelSearchParser(new OpenAiService($agent, $today), new HotelSearchMapper());
        $search = $parser->parse($message);

        self::assertSame(['swimming_pool', 'pets_allowed'], $search->amenities);
        self::assertSame([
            'rating' => 'MEDIUM',
            'price' => 'MEDIUM',
            'location' => 'MEDIUM',
            'amenity' => 'MEDIUM',
        ], $search->rankingWeights);
    }
}
