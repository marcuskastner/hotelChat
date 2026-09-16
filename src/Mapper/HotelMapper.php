<?php

namespace App\Mapper;

use App\Entity\Hotel;

class HotelMapper
{
    public function map(array $content): Hotel
    {
        $hotel = new Hotel();

        $hotel
            ->setId((string)$content['property_id'])
            ->setName($content['name'])
            ->setCity($content['address']['city'] ?? null)
            ->setState($content['address']['state_province_code'] ?? null)
            ->setCountry($content['address']['country_code'] ?? null)
            ->setLatitude(
                isset($content['location']['coordinates']['latitude'])
                    ? (float)$content['location']['coordinates']['latitude']
                    : null
            )
            ->setLongitude(
                isset($content['location']['coordinates']['longitude'])
                    ? (float)$content['location']['coordinates']['longitude']
                    : null
            )
            ->setStarRating(
                isset($content['ratings']['property']['rating'])
                    ? (float)$content['ratings']['property']['rating']
                    : null
            )
            ->setGuestRating(
                isset($content['ratings']['guest']['overall'])
                    ? (float)$content['ratings']['guest']['overall']
                    : null
            )
            ->setGuestReviewCount(
                (int)($content['ratings']['guest']['count'] ?? 0)
            )
            ->setPropertyType(
                $content['category']['name'] ?? null
            )
            ->setAmenities(
                $this->mapAmenities($content['amenities'] ?? [])
            );

        return $hotel;
    }

    /**
     * @param array<string, array<string, mixed>> $amenities
     *
     * @return list<string>
     */
    private function mapAmenities(array $amenities): array
    {
        $categories = [];

        foreach ($amenities as $amenity) {
            foreach ($amenity['categories'] ?? [] as $category) {
                $categories[] = $category;
            }
        }

        return array_values(array_unique($categories));
    }
}
