<?php

namespace App\Tests\Entity;

use App\Entity\HotelSearch;
use PHPUnit\Framework\TestCase;

class HotelSearchTest extends TestCase
{
    public function testDefaultSearch(): void
    {
        $search = new HotelSearch();

        self::assertNull($search->city);
        self::assertNull($search->state);
        self::assertNull($search->checkIn);
        self::assertNull($search->checkOut);
        self::assertSame(1, $search->adults);
        self::assertSame(0, $search->children);
        self::assertSame(1, $search->rooms);
        self::assertSame([], $search->amenities);
        self::assertSame([
            'rating' => 'MEDIUM',
            'price' => 'MEDIUM',
            'location' => 'MEDIUM',
            'amenity' => 'MEDIUM',
        ], $search->rankingWeights);
    }

    public function testCanCreateSearch(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
            amenities: ['swimming_pool'],
        );

        self::assertSame('San Diego', $search->city);
        self::assertSame('CA', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(2, $search->adults);
        self::assertSame(1, $search->rooms);
        self::assertSame(['swimming_pool'], $search->amenities);
    }

    public function testDistanceInMilesReturnsNullWhenCoordinatesAreMissing(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
        );

        self::assertNull($search->distanceInMiles(32.718702, -117.167743));
    }

    public function testDistanceInMilesFromSearchCoordinates(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            latitude: 32.7157,
            longitude: -117.1611,
        );

        self::assertEqualsWithDelta(0.0, $search->distanceInMiles(32.7157, -117.1611), 0.0001);
        self::assertEqualsWithDelta(0.45, $search->distanceInMiles(32.718702, -117.167743), 0.1);
        self::assertNull($search->distanceInMiles(null, -117.167743));
    }
}
