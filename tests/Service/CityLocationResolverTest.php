<?php

namespace App\Tests\Service;

use App\Entity\HotelSearch;
use App\Service\CityLocationResolver;
use PHPUnit\Framework\TestCase;

class CityLocationResolverTest extends TestCase
{
    public function testResolve(): void
    {
        $hotelSearch = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1
        );
        $resolver = new CityLocationResolver();
        $resolver->resolve($hotelSearch);
        $this->assertNotNull($hotelSearch->getLatitude());
        $this->assertNotNull($hotelSearch->getLongitude());
    }

    public function testResolveWithInvalidCity(): void
    {
        $hotelSearch = new HotelSearch(
            city: 'Invalid City',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1
        );
        $resolver = new CityLocationResolver();
        $this->expectException(\InvalidArgumentException::class);
        $resolver->resolve($hotelSearch);
    }
}
