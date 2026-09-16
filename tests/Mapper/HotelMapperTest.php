<?php

namespace App\Tests\Mapper;

use App\Mapper\HotelMapper;
use PHPUnit\Framework\TestCase;

class HotelMapperTest extends TestCase
{
    public function testMapUsesExpediaAmenityCategoriesOnly(): void
    {
        $hotel = (new HotelMapper())->map([
            'property_id' => '124966334',
            'name' => 'Le Méridien San Diego Downtown',
            'address' => [
                'city' => 'San Diego',
                'state_province_code' => 'CA',
                'country_code' => 'US',
            ],
            'location' => [
                'coordinates' => [
                    'latitude' => 32.718702,
                    'longitude' => -117.167743,
                ],
            ],
            'ratings' => [
                'property' => [
                    'rating' => '4.0',
                    'type' => 'Star',
                ],
            ],
            'category' => [
                'name' => 'Hotel',
            ],
            'amenities' => [
                '2821' => [
                    'id' => '2821',
                    'name' => 'Number of outdoor pools - 1',
                    'value' => '1',
                ],
                '9' => [
                    'id' => '9',
                    'name' => 'Fitness facilities',
                    'categories' => ['gym'],
                ],
                '3864' => [
                    'id' => '3864',
                    'name' => 'Valet parking (surcharge)',
                    'categories' => ['parking'],
                ],
                '2131' => [
                    'id' => '2131',
                    'name' => 'Meeting rooms',
                    'categories' => ['meeting_facility'],
                ],
                'unknown' => [
                    'id' => 'x',
                    'name' => 'Not a supported category',
                    'categories' => ['not_a_real_category'],
                ],
            ],
        ]);

        self::assertSame('124966334', $hotel->getId());
        self::assertSame('Le Méridien San Diego Downtown', $hotel->getName());
        self::assertSame('San Diego', $hotel->getCity());
        self::assertSame('CA', $hotel->getState());
        self::assertSame(4.0, $hotel->getStarRating());
        self::assertNull($hotel->getGuestRating());
        self::assertSame(['gym', 'parking', 'meeting_facility', 'not_a_real_category'], $hotel->getAmenities());
        self::assertFalse($hotel->hasAmenity('swimming_pool'));
    }
}
