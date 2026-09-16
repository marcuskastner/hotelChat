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
            amenities: ['swimming_pool'],
        );

        $withPool = (new Hotel())
            ->setId('1')
            ->setName('Pool Hotel')
            ->setStarRating(4)
            ->setAmenities(['swimming_pool']);

        $withoutPool = (new Hotel())
            ->setId('2')
            ->setName('No Pool Hotel')
            ->setStarRating(5)
            ->setAmenities([]);

        $ranked = (new HotelRankerService())->rank($search, [$withoutPool, $withPool]);

        self::assertSame([$withPool, $withoutPool], $ranked);
        self::assertGreaterThan($withoutPool->getScore(), $withPool->getScore());
    }

    public function testRankUsesSearchRankingWeights(): void
    {
        $search = new HotelSearch(
            city: 'Portland',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            amenities: ['gym'],
            rankingWeights: [
                'rating' => 'HIGH',
                'price' => 'NA',
                'location' => 'NA',
                'amenity' => 'LOW',
            ],
        );

        $withGym = (new Hotel())
            ->setId('1')
            ->setName('Gym Hotel')
            ->setStarRating(3)
            ->setAmenities(['gym']);

        $higherRated = (new Hotel())
            ->setId('2')
            ->setName('Rated Hotel')
            ->setStarRating(5)
            ->setAmenities([]);

        $ranked = (new HotelRankerService())->rank($search, [$withGym, $higherRated]);

        self::assertSame([$higherRated, $withGym], $ranked);
        self::assertGreaterThan($withGym->getScore(), $higherRated->getScore());
    }

    public function testRankFallsBackToStarRatingWhenGuestRatingIsMissing(): void
    {
        $search = new HotelSearch(
            city: 'Portland',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
        );

        $fourStar = (new Hotel())
            ->setId('1')
            ->setName('Four Star')
            ->setStarRating(4);

        $twoStar = (new Hotel())
            ->setId('2')
            ->setName('Two Star')
            ->setStarRating(2);

        $ranked = (new HotelRankerService())->rank($search, [$twoStar, $fourStar]);

        self::assertSame([$fourStar, $twoStar], $ranked);
        self::assertGreaterThan($twoStar->getScore(), $fourStar->getScore());
    }

    public function testRankPrefersHotelsCloserToTheSearchCoordinates(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
        );

        $downtown = (new Hotel())
            ->setId('1')
            ->setName('Downtown')
            ->setStarRating(4)
            ->setDistance(0.4);

        $farther = (new Hotel())
            ->setId('2')
            ->setName('Farther')
            ->setStarRating(4)
            ->setDistance(8.0);

        $ranked = (new HotelRankerService())->rank($search, [$farther, $downtown]);

        self::assertSame([$downtown, $farther], $ranked);
        self::assertGreaterThan($farther->getScore(), $downtown->getScore());
    }

    public function testRankIgnoresPriceWeightUntilPriceDataExists(): void
    {
        $search = new HotelSearch(
            city: 'Portland',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            rankingWeights: [
                'rating' => 'LOW',
                'price' => 'HIGH',
                'location' => 'NA',
                'amenity' => 'NA',
            ],
        );

        $fourStar = (new Hotel())
            ->setId('1')
            ->setName('Four Star')
            ->setStarRating(4);

        $twoStar = (new Hotel())
            ->setId('2')
            ->setName('Two Star')
            ->setStarRating(2);

        $ranked = (new HotelRankerService())->rank($search, [$twoStar, $fourStar]);

        self::assertSame([$fourStar, $twoStar], $ranked);
        self::assertGreaterThan($twoStar->getScore(), $fourStar->getScore());
        self::assertEqualsWithDelta(0.8, $fourStar->getScore(), 0.0001);
    }

    public function testRankOmitsSignalsMarkedNa(): void
    {
        $search = new HotelSearch(
            city: 'San Diego',
            state: 'CA',
            checkIn: '2026-09-19',
            checkOut: '2026-09-20',
            rankingWeights: [
                'rating' => 'HIGH',
                'price' => 'NA',
                'location' => 'NA',
                'amenity' => 'NA',
            ],
        );

        $close = (new Hotel())
            ->setId('1')
            ->setName('Close')
            ->setStarRating(4)
            ->setDistance(0.4);

        $far = (new Hotel())
            ->setId('2')
            ->setName('Far')
            ->setStarRating(4)
            ->setDistance(8.0);

        $ranked = (new HotelRankerService())->rank($search, [$far, $close]);

        self::assertEqualsWithDelta($close->getScore(), $far->getScore(), 0.0001);
    }
}
