<?php

namespace App\Entity;

class HotelSearch
{
    public function __construct(
        public ?string $destination = null,
        public ?string $checkIn = null,
        public ?string $checkOut = null,
        public ?int $adults = 1,
        public ?int $children = 0,
        public ?int $rooms = 1,
        public ?array $amenities = [],
    ) {
    }
}
