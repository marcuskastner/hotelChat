<?php

namespace App\Action;

use App\Entity\HotelSearch;
use App\Mapper\HotelCandidateMapper;
use App\Repository\HotelToChannelSourceRepository;
use App\Service\CityLocationResolver;
use App\Service\ContentServiceClient;
use App\Service\CoordinatePolygonService;

class HotelCandidatesFromChatSearchAction
{
    public function __construct(
        private CityLocationResolver $locationResolver,
        private ContentServiceClient $contentServiceClient,
        private CoordinatePolygonService $coordinatePolygonService,
        private HotelCandidateMapper $hotelCandidateMapper,
        private HotelToChannelSourceRepository $hotelToChannelSourceRepository,
    ) {
    }

    public function getHotelCandidates(HotelSearch $search): array
    {
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
