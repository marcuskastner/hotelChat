<?php

namespace App\Tests\Entity;

use App\Entity\Hotel;
use App\Entity\HotelSearch;
use PHPUnit\Framework\TestCase;

class HotelTest extends TestCase
{
    public function testSetDistanceFromUsesHotelSearchCoordinates(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            latitude: 32.7157,
            longitude: -117.1611,
        );

        $hotel = (new Hotel())
            ->setId('124966334')
            ->setName('Le Méridien San Diego Downtown')
            ->setLatitude(32.718702)
            ->setLongitude(-117.167743)
            ->setDistanceFrom($search);

        self::assertEqualsWithDelta(0.45, $hotel->getDistance(), 0.1);
    }

    public function testSetDistanceFromLeavesDistanceNullWhenCoordinatesAreMissing(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
        );

        $hotel = (new Hotel())
            ->setId('1')
            ->setName('Unknown Location')
            ->setDistanceFrom($search);

        self::assertNull($hotel->getDistance());
    }
}
