<?php

namespace App\Action;

use App\Entity\HotelSearch;
use App\Mapper\HotelCandidateMapper;
use App\Repository\HotelToChannelSourceRepository;
use App\Service\CityLocationResolver;
use App\Service\ContentServiceClient;
use App\Service\HotelSearchParser;
use App\Service\HotelSearchValidator;
use App\Service\CoordinatePolygonService;

class HotelCandidatesFromChatSearchAction
{
    private HotelSearchParser $parser;
    private HotelSearchValidator $validator;
    private CityLocationResolver $locationResolver;
    private ContentServiceClient $contentServiceClient;
    private CoordinatePolygonService $coordinatePolygonService;
    private HotelCandidateMapper $hotelCandidateMapper;
    private HotelToChannelSourceRepository $hotelToChannelSourceRepository;

    public function __construct(
        HotelSearchParser $parser,
        HotelSearchValidator $validator,
        CityLocationResolver $locationResolver,
        ContentServiceClient $contentServiceClient,
        CoordinatePolygonService $coordinatePolygonService,
        HotelCandidateMapper $hotelCandidateMapper,
        HotelToChannelSourceRepository $hotelToChannelSourceRepository
    ){
        $this->parser = $parser;
        $this->validator = $validator;
        $this->locationResolver = $locationResolver;
        $this->contentServiceClient = $contentServiceClient;
        $this->coordinatePolygonService = $coordinatePolygonService;
        $this->hotelCandidateMapper = $hotelCandidateMapper;
        $this->hotelToChannelSourceRepository = $hotelToChannelSourceRepository;
    }

    public function getHotelCandidates(string $message): array
    {
        if($message === 'test') {
            $search = new HotelSearch(
                city: 'San Diego',
                state: 'CA',
                checkIn: '2026-09-15',
                checkOut: '2026-09-17',
                adults: 2,
                rooms: 1,
                amenities: ['pool'],
            );
        }else{
            $search = $this->parser->parse($message);
        }
        $this->validator->validate($search);
        $this->locationResolver->resolve($search);
        $coordinates = $this->coordinatePolygonService->generate($search);
        $data = $this->contentServiceClient->getExpediaPropertyIds($coordinates);

        if (!isset($data['result']) || !is_array($data['result'])) {
            throw new \RuntimeException('Invalid response from content service: missing properties');
        }

        $candidates = array_map([$this->hotelCandidateMapper, 'map'], $data['result']);
        $this->hotelToChannelSourceRepository->decorateAresHotelIds($candidates);

        return array_filter($candidates, function ($candidate) {
            return $candidate->getAresHotelId() !== null;
        });
    }
}
