<?php

namespace App\Action;

use App\Entity\HotelSearch;
use App\Service\CityLocationResolver;
use App\Service\ContentServiceClient;
use App\Service\HotelSearchParser;
use App\Service\HotelSearchValidator;
use App\Service\CoordinatePolygonService;

class HotelChatSearchAction
{
    private HotelSearchParser $parser;
    private HotelSearchValidator $validator;
    private CityLocationResolver $locationResolver;
    private ContentServiceClient $contentServiceClient;
    private CoordinatePolygonService $coordinatePolygonService;
    public function __construct(
        HotelSearchParser $parser,
        HotelSearchValidator $validator,
        CityLocationResolver $locationResolver,
        ContentServiceClient $contentServiceClient,
        CoordinatePolygonService $coordinatePolygonService
    ){
        $this->parser = $parser;
        $this->validator = $validator;
        $this->locationResolver = $locationResolver;
        $this->contentServiceClient = $contentServiceClient;
        $this->coordinatePolygonService = $coordinatePolygonService;
    }

    public function execute(string $message): HotelSearch
    {
        $search = $this->parser->parse($message);
        $this->validator->validate($search);
        $this->locationResolver->resolve($search);
        $coordinates = $this->coordinatePolygonService->generate($search);
        $properties = $this->contentServiceClient->getExpediaProperties($coordinates);

        return $search;
    }
}
