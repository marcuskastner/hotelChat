<?php

namespace App\Service;

use App\Entity\HotelSearch;
use App\Mapper\HotelSearchMapper;

readonly class HotelSearchParser
{
    public function __construct(
        private OpenAiService $openAI,
        private HotelSearchMapper $mapper
    ) {
    }

    public function parse(string $message): HotelSearch
    {
        $data = $this->openAI->ask($message);

        return $this->mapper->map($data);
    }
}
