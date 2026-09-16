<?php

namespace App\Tests\Service;

use App\Entity\Hotel;
use App\Entity\HotelSearch;
use App\Service\HotelRankerService;
use PHPUnit\Framework\TestCase;

class HotelRankerServiceTest extends TestCase
{
    public function testRankOrdersHotelsByScoreUsingSearchAmenities(): void
    {
        $search = new HotelSearch(
            city: 'Portland',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            amenities: ['pool'],
        );

        $withPool = (new Hotel())
            ->setId('1')
            ->setName('Pool Hotel')
            ->setGuestRating(8)
            ->setAmenities(['pool']);

        $withoutPool = (new Hotel())
            ->setId('2')
            ->setName('No Pool Hotel')
            ->setGuestRating(10)
            ->setAmenities([]);

        $ranked = (new HotelRankerService())->rank($search, [$withoutPool, $withPool]);

        self::assertSame([$withPool, $withoutPool], $ranked);
        self::assertGreaterThan($withoutPool->getScore(), $withPool->getScore());
    }
}
