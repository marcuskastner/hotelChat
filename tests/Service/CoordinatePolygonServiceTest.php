<?php

namespace App\Tests\Service;

use App\Entity\HotelSearch;
use App\Service\CoordinatePolygonService;
use PHPUnit\Framework\TestCase;

class CoordinatePolygonServiceTest extends TestCase
{
    public function testGeneratePolygonCoordinates()
    {
        $hotelSearch = new HotelSearch();
        $hotelSearch->setLatitude(37.7749); // San Francisco latitude
        $hotelSearch->setLongitude(-122.4194); // San Francisco longitude

        $coordinatePolygonService = new CoordinatePolygonService();
        $polygon = $coordinatePolygonService->generate($hotelSearch);

        // Assert that the polygon has 9 points (8 + 1 to close the polygon)
        $this->assertCount(9, $polygon['coordinates'][0]);

        // Assert that the first and last points are the same (closing the polygon)
        $this->assertEquals($polygon['coordinates'][0][0], end($polygon['coordinates'][0]));
    }
}
