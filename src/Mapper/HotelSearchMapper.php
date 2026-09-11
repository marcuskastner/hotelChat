<?php

namespace App\Mapper;

use App\Entity\HotelSearch;

class HotelSearchMapper
{
    public function map(array $data): HotelSearch
    {
        return new HotelSearch(
            destination: $data['destination'],
            checkIn: $data['checkIn'],
            checkOut: $data['checkOut'],
            adults: $data['adults'],
            rooms: $data['rooms'],
            amenities: $data['amenities'],
        );
    }
}
