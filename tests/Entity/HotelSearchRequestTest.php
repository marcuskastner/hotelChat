<?php

namespace App\Tests\Entity;

use App\Entity\HotelSearchRequest;
use PHPUnit\Framework\TestCase;

class HotelSearchRequestTest extends TestCase
{
    public function testDefaultSearchRequest(): void
    {
        $request = new HotelSearchRequest();

        self::assertNull($request->destination);
        self::assertNull($request->checkIn);
        self::assertNull($request->checkOut);
        self::assertSame(2, $request->adults);
        self::assertSame(1, $request->rooms);
        self::assertSame([], $request->amenities);
    }

    public function testCanCreateSearchRequest(): void
    {
        $request = new HotelSearchRequest(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
            amenities: ['pool'],
        );

        self::assertSame('San Diego', $request->destination);
        self::assertSame('2026-09-15', $request->checkIn);
        self::assertSame('2026-09-17', $request->checkOut);
        self::assertSame(2, $request->adults);
        self::assertSame(1, $request->rooms);
        self::assertSame(['pool'], $request->amenities);
    }
}
