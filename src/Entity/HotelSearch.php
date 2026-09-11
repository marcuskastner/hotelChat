<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class HotelSearch
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $destination = null,

        #[Assert\Date]
        public ?string $checkIn = null,

        #[Assert\Date]
        public ?string $checkOut = null,

        #[Assert\Positive]
        public ?int $adults = 1,

        #[Assert\PositiveOrZero]
        public ?int $children = 0,

        #[Assert\Positive]
        public ?int $rooms = 1,

        public ?array $amenities = [],
    ) {
    }
}
