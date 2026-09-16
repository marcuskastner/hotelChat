<?php

namespace App\Service;

use App\Entity\HotelCandidate;
use App\Mapper\HotelMapper;

class HotelContentService{
    public function __construct(
        private readonly HotelMapper $hotelMapper,
        private readonly ContentServiceClient $client
    ) {}
    public function getHotelsContent(array $candidates): array
    {
        $expediaIds = array_map(fn(HotelCandidate $candidate) => $candidate->expediaPropertyId, $candidates);
        $data = $this->client->getPropertiesContent($expediaIds, ['name', 'address','ratings','location', 'category', 'amenities']);
        return array_map(fn(array $hotelData) => $this->hotelMapper->map($hotelData), $data['result']);
    }
}
