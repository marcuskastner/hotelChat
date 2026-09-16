<?php

namespace App\Service;

use App\Entity\Hotel;
use App\Entity\HotelSearch;
use App\Entity\RankingImportance;

class HotelRankerService
{
    /**
     * Price is omitted until Hotel has availability/rate data.
     */
    private const array ACTIVE_SIGNALS = ['rating', 'location', 'amenity'];

    /**
     * @param Hotel[] $hotels
     *
     * @return Hotel[]
     */
    public function rank(HotelSearch $search, array $hotels): array
    {
        $weights = $this->normalizedWeights($search);

        foreach ($hotels as $hotel) {
            $hotel->setScore(
                $this->score($search, $hotel, $weights)
            );
        }

        usort(
            $hotels,
            fn (Hotel $a, Hotel $b) => $b->getScore() <=> $a->getScore()
        );

        return $hotels;
    }

    /**
     * @param array<string, float> $weights
     */
    private function score(HotelSearch $search, Hotel $hotel, array $weights): float
    {
        return
            ($this->ratingScore($hotel) * $weights['rating']) +
            ($this->locationScore($hotel) * $weights['location']) +
            ($this->amenityScore($search, $hotel) * $weights['amenity']);
    }

    /**
     * @return array<string, float>
     */
    private function normalizedWeights(HotelSearch $search): array
    {
        $raw = $search->getRankingWeights() ?: [
            'rating' => RankingImportance::MEDIUM,
            'price' => RankingImportance::MEDIUM,
            'location' => RankingImportance::MEDIUM,
            'amenity' => RankingImportance::MEDIUM,
        ];
        $active = [];

        foreach (self::ACTIVE_SIGNALS as $signal) {
            $importance = $raw[$signal] ?? RankingImportance::NA;
            $active[$signal] = RankingImportance::weight(
                is_string($importance) ? $importance : RankingImportance::NA
            );
        }

        $sum = array_sum($active);

        if ($sum <= 0) {
            $equal = 1 / count(self::ACTIVE_SIGNALS);

            return array_fill_keys(self::ACTIVE_SIGNALS, $equal);
        }

        foreach ($active as $signal => $weight) {
            $active[$signal] = $weight / $sum;
        }

        return $active;
    }

    private function ratingScore(Hotel $hotel): float
    {
        $rating = $hotel->getGuestRating() ?? $hotel->getStarRating();

        if ($rating === null) {
            return 0;
        }

        // Expedia guest and star ratings are out of 5.
        return min($rating / 5, 1);
    }

    private function locationScore(Hotel $hotel): float
    {
        $distance = $hotel->getDistance();

        if ($distance === null) {
            return 0;
        }

        // 0 miles = 1.0, 10+ miles = 0.0
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
