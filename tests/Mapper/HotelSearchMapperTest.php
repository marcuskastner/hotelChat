<?php

namespace App\Tests\Mapper;

use App\Entity\RankingImportance;
use App\Mapper\HotelSearchMapper;
use PHPUnit\Framework\TestCase;

class HotelSearchMapperTest extends TestCase
{
    public function testMapRankingWeightsFromImportanceLabels(): void
    {
        $search = (new HotelSearchMapper())->map([
            'city' => 'San Diego',
            'state' => 'CA',
            'checkIn' => '2026-09-19',
            'checkOut' => '2026-09-20',
            'adults' => 2,
            'children' => 1,
            'amenities' => ['swimming_pool', 'airport_transfer'],
            'rankingWeights' => [
                'rating' => 'HIGH',
                'price' => 'NA',
                'location' => 'na',
                'amenity' => 'HIGH',
            ],
        ]);

        self::assertSame([
            'rating' => RankingImportance::HIGH,
            'price' => RankingImportance::NA,
            'location' => RankingImportance::NA,
            'amenity' => RankingImportance::HIGH,
        ], $search->rankingWeights);
    }

    public function testMapUnknownRankingWeightBecomesNa(): void
    {
        $search = (new HotelSearchMapper())->map([
            'city' => 'San Diego',
            'state' => 'CA',
            'rankingWeights' => [
                'rating' => 0.6,
                'price' => 0.05,
                'location' => 'unknown',
                'amenity' => 'HIGH',
            ],
        ]);

        self::assertSame([
            'rating' => RankingImportance::NA,
            'price' => RankingImportance::NA,
            'location' => RankingImportance::NA,
            'amenity' => RankingImportance::HIGH,
        ], $search->rankingWeights);
    }
}
