<?php

namespace App\Service;

use App\Entity\HotelSearch;

class HotelSearchParser
{
    public function parse(string $message): HotelSearch
    {
        // Send message to LLM
        // Get structured response
        // Convert response to HotelSearch
        return new HotelSearch();
    }
}
