<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class HotelSearch
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $city = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 2)]
        public ?string $state = null,

        #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
        #[Assert\Date]
        #[Assert\NotBlank]
        public ?string $checkIn = null,

        #[Assert\Regex('/^\d{4}-\d{2}-\d{2}$/')]
        #[Assert\Date]
        #[Assert\NotBlank]
        public ?string $checkOut = null,

        #[Assert\Positive]
        public int $adults = 1,

        #[Assert\PositiveOrZero]
        public int $children = 0,

        #[Assert\Positive]
        public int $rooms = 1,

        #[Assert\All([
            new Assert\Choice(choices: ExpediaAmenities::EXPEDIA_AMENITIES),
        ])]
        public array $amenities = [],

        public ?float $latitude = null,
        public ?float $longitude = null
    ){

    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): void
    {
        $this->state = $state;
    }

    public function getCheckIn(): ?string
    {
        return $this->checkIn;
    }

    public function setCheckIn(?string $checkIn): void
    {
        $this->checkIn = $checkIn;
    }

    public function getCheckOut(): ?string
    {
        return $this->checkOut;
    }

    public function setCheckOut(?string $checkOut): void
    {
        $this->checkOut = $checkOut;
    }

    public function getAdults(): int
    {
        return $this->adults;
    }

    public function setAdults(int $adults): void
    {
        $this->adults = $adults;
    }

    public function getChildren(): int
    {
        return $this->children;
    }

    public function setChildren(int $children): void
    {
        $this->children = $children;
    }

    public function getRooms(): int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): void
    {
        $this->rooms = $rooms;
    }

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function setAmenities(array $amenities): void
    {
        $this->amenities = $amenities;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }
}
