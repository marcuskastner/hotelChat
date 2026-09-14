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
            amenities: ['pool'],
        );

        self::assertSame('San Diego', $search->city);
        self::assertSame('CA', $search->state);
        self::assertSame('2026-09-15', $search->checkIn);
        self::assertSame('2026-09-17', $search->checkOut);
        self::assertSame(2, $search->adults);
        self::assertSame(1, $search->rooms);
        self::assertSame(['pool'], $search->amenities);
    }
}
