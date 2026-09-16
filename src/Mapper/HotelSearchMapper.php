<?php

namespace App\Mapper;

use App\Entity\HotelSearch;
use App\Entity\RankingImportance;

class HotelSearchMapper
{
    private const array DEFAULT_RANKING_WEIGHTS = [
        'rating' => RankingImportance::MEDIUM,
        'price' => RankingImportance::MEDIUM,
        'location' => RankingImportance::MEDIUM,
        'amenity' => RankingImportance::MEDIUM,
    ];

    public function map(array $data): HotelSearch
    {
        return new HotelSearch(
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            checkIn: $data['checkIn'] ?? null,
            checkOut: $data['checkOut'] ?? null,
            adults: $data['adults'] ?? 1,
            children: $data['children'] ?? 0,
            rooms: $data['rooms'] ?? 1,
            amenities: $data['amenities'] ?? [],
            rankingWeights: $this->mapRankingWeights($data['rankingWeights'] ?? []),
        );
    }

    /**
     * @param array<string, mixed> $weights
     *
     * @return array<string, string>
     */
    private function mapRankingWeights(array $weights): array
    {
        if ($weights === []) {
            return self::DEFAULT_RANKING_WEIGHTS;
        }

        return [
            'rating' => $this->mapImportance($weights['rating'] ?? RankingImportance::NA),
            'price' => $this->mapImportance($weights['price'] ?? RankingImportance::NA),
            'location' => $this->mapImportance($weights['location'] ?? RankingImportance::NA),
            'amenity' => $this->mapImportance($weights['amenity'] ?? RankingImportance::NA),
        ];
    }

    private function mapImportance(mixed $value): string
    {
        if (!is_string($value)) {
            return RankingImportance::NA;
        }

        $value = strtoupper($value);

        if (!in_array($value, RankingImportance::VALUES, true)) {
            return RankingImportance::NA;
        }

        return $value;
    }
}
