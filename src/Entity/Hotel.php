<?php

namespace App\Entity;

class Hotel
{
    private string $id;
    private string $name;

    private ?string $city = null;
    private ?string $state = null;
    private ?string $country = null;

    private ?float $latitude = null;
    private ?float $longitude = null;

    private ?float $starRating = null;
    private ?float $guestRating = null;
    private int $guestReviewCount = 0;

    private ?string $propertyType = null;

    /**
     * Normalized amenity categories.
     *
     * Example:
     * [
     *     'swimming_pool',
     *     'wifi',
     *     'free_breakfast',
     *     'parking',
     *     'gym'
     * ]
     */
    private array $amenities = [];

    /**
     * Distance from the requested destination in miles.
     */
    private ?float $distance = null;

    /**
     * Ranking score.
     */
    private ?float $score = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getStarRating(): ?float
    {
        return $this->starRating;
    }

    public function setStarRating(?float $starRating): self
    {
        $this->starRating = $starRating;

        return $this;
    }

    public function getGuestRating(): ?float
    {
        return $this->guestRating;
    }

    public function setGuestRating(?float $guestRating): self
    {
        $this->guestRating = $guestRating;

        return $this;
    }

    public function getGuestReviewCount(): int
    {
        return $this->guestReviewCount;
    }

    public function setGuestReviewCount(int $guestReviewCount): self
    {
        $this->guestReviewCount = $guestReviewCount;

        return $this;
    }

    public function getPropertyType(): ?string
    {
        return $this->propertyType;
    }

    public function setPropertyType(?string $propertyType): self
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getAmenities(): array
    {
        return $this->amenities;
    }

    public function setAmenities(array $amenities): self
    {
        $this->amenities = $amenities;

        return $this;
    }

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities, true);
    }


    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(?float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function setDistanceFrom(HotelSearch $search): self
    {
        $this->distance = $search->distanceInMiles($this->latitude, $this->longitude);

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }
}
