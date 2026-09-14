<?php

namespace App\Service;

use App\Entity\HotelSearch;

class CoordinatePolygonService
{
    private const RADIUS_KM = 15;
    private const POLYGON_POINTS = 8;
    private const EARTH_RADIUS_KM = 6371.0088;

    public function generate(HotelSearch $hotelSearch): array
    {
        $latitude = $hotelSearch->getLatitude();
        $longitude = $hotelSearch->getLongitude();

        $lat = deg2rad($latitude);
        $lng = deg2rad($longitude);

        $angularDistance = self::RADIUS_KM / self::EARTH_RADIUS_KM;

        $coordinates = [];

        for ($i = 0; $i < self::POLYGON_POINTS; $i++) {
            // Start at north and move clockwise.
            $bearing = 2 * M_PI * $i / self::POLYGON_POINTS;

            $pointLat = asin(
                sin($lat) * cos($angularDistance)
                + cos($lat)
                * sin($angularDistance)
                * cos($bearing)
            );

            $pointLng = $lng + atan2(
                    sin($bearing)
                    * sin($angularDistance)
                    * cos($lat),
                    cos($angularDistance)
                    - sin($lat) * sin($pointLat)
                );

            $coordinates[] = [
                rad2deg($pointLng),
                rad2deg($pointLat),
            ];
        }

        // Close the polygon by repeating the first point.
        $coordinates[] = $coordinates[0];

        return [
            'coordinates' => [
                $coordinates,
            ],
        ];
    }
}
