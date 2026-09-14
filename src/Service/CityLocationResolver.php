<?php

namespace App\Service;

use App\Entity\HotelSearch;

class CityLocationResolver
{
    private array $cityLocationData;
    public function __construct()
    {
        $this->cityLocationData = json_decode(file_get_contents(__DIR__ . "/../Data/us_latlng.json"), true);
    }

    public function resolve(HotelSearch $hotelSearch): void
    {
        $city = ucwords($hotelSearch->getCity());
        $state = strtoupper($hotelSearch->getState());

        if(isset($this->cityLocationData[$state]['cities'][$city])) {
            $location = $this->cityLocationData[$state]['cities'][$city];
            $hotelSearch->setLatitude($location['lat']);
            $hotelSearch->setLongitude($location['lng']);
            return;
        }
        throw new \InvalidArgumentException("Location not found for city: $city, state: $state");
    }

}
