<?php

namespace App\Action;

use App\Entity\HotelSearch;
use App\Service\HotelSearchParser;
use App\Service\HotelSearchValidator;

class HotelSearchFromChatAction
{
    public function __construct(
        private HotelSearchParser $parser,
        private HotelSearchValidator $validator,
    ) {
    }

    public function getHotelSearch(string $message): HotelSearch
    {
        if ($message === 'test') {
            $search = new HotelSearch(
                city: 'San Diego',
                state: 'CA',
                checkIn: '2026-09-15',
                checkOut: '2026-09-17',
                adults: 2,
                rooms: 1,
                amenities: ['pool'],
            );
        } else {
            $search = $this->parser->parse($message);
        }

        $this->validator->validate($search);

        return $search;
    }
}
