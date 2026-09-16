<?php

namespace App\Service;

use App\Entity\Hotel;
use App\Entity\HotelSearch;

class HotelRankerService
{
    private const float RATING_WEIGHT = 0.25;
    private const float PRICE_WEIGHT = 0.25;
    private const float LOCATION_WEIGHT = 0.25;
    private const float AMENITY_WEIGHT = 0.25;

    /**
     * @param Hotel[] $hotels
     *
     * @return Hotel[]
     */
    public function rank(HotelSearch $search, array $hotels): array
    {
        foreach ($hotels as $hotel) {
            $hotel->setScore(
                $this->score($search, $hotel)
            );
        }

        usort(
            $hotels,
            fn (Hotel $a, Hotel $b) => $b->getScore() <=> $a->getScore()
        );

        return $hotels;
    }

    private function score(HotelSearch $search, Hotel $hotel): float
    {
        return
            ($this->ratingScore($hotel) * self::RATING_WEIGHT) +
            ($this->priceScore($hotel) * self::PRICE_WEIGHT) +
            ($this->locationScore($search, $hotel) * self::LOCATION_WEIGHT) +
            ($this->amenityScore($search, $hotel) * self::AMENITY_WEIGHT);
    }

    private function ratingScore(Hotel $hotel): float
    {
        $rating = $hotel->getGuestRating();

        if ($rating === null) {
            return 0;
        }

        // Expedia guest ratings are out of 5.
        return min($rating / 5, 1);
    }

    private function priceScore(Hotel $hotel): float
    {
        // We will implement this once Hotel has its availability/price data.
        return 0;
    }

    private function locationScore(HotelSearch $search, Hotel $hotel): float
    {
        $distance = $hotel->getDistance();

        if ($distance === null) {
            return 0;
        }

        // Example: 0 miles = 1.0, 10+ miles = 0.0
        return max(0, 1 - ($distance / 10));
    }

    private function amenityScore(HotelSearch $search, Hotel $hotel): float
    {
        $requestedAmenities = $search->getAmenities();

        if (count($requestedAmenities) === 0) {
            return 1;
        }

        $matches = 0;

        foreach ($requestedAmenities as $amenity) {
            if ($hotel->hasAmenity($amenity)) {
                $matches++;
            }
        }

        return $matches / count($requestedAmenities);
    }
}
