<?php

namespace App\Action;

use App\Entity\HotelSearch;
use App\Service\CityLocationResolver;
use App\Service\HotelSearchParser;
use App\Service\HotelSearchValidator;

class HotelChatSearchAction
{
    private HotelSearchParser $parser;
    private HotelSearchValidator $validator;
    private CityLocationResolver $locationResolver;
    public function __construct(
        HotelSearchParser $parser,
        HotelSearchValidator $validator,
        CityLocationResolver $locationResolver
    ){
        $this->parser = $parser;
        $this->validator = $validator;
        $this->locationResolver = $locationResolver;
    }

    public function execute(string $message): HotelSearch
    {
        $search = $this->parser->parse($message);
        $this->validator->validate($search);
        $this->locationResolver->resolve($search);
        return $search;
    }
}
