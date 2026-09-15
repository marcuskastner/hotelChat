<?php

namespace App\Mapper;

use App\Entity\HotelCandidate;

class HotelCandidateMapper
{
    public function map(array $data): HotelCandidate
    {
        $candidate = new HotelCandidate();

        $candidate->expediaPropertyId = $data['property_id'];

        return $candidate;
    }
}
