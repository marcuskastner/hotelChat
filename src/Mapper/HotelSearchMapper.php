<?php

namespace App\Mapper;

use App\Entity\HotelSearch;

class HotelSearchMapper
{
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
        );
    }
}
