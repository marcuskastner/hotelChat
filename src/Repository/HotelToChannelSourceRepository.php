<?php

namespace App\Repository;

use App\Entity\HotelCandidate;
use App\Entity\HotelToChannelSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HotelToChannelSource>
 */
class HotelToChannelSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HotelToChannelSource::class);
    }

    /**
     * @param list<HotelCandidate> $hotelCandidates
     */
    public function decorateAresHotelIds(array $hotelCandidates): void
    {
        if ($hotelCandidates === []) {
            return;
        }

        $expediaIds = array_map(
            static fn (HotelCandidate $candidate): string => (string)$candidate->expediaPropertyId,
            $hotelCandidates,
        );

        $sources = $this->findBy([
            'channelName' => HotelToChannelSource::CHANNEL_NAME_EXPEDIA,
            'channelHotelID' => $expediaIds,
        ]);

        $hotelIdByExpediaId = [];
        foreach ($sources as $source) {
            $hotelIdByExpediaId[$source->getChannelHotelID()] = $source->getHotelID();
        }

        foreach ($hotelCandidates as $candidate) {
            $aresId = $hotelIdByExpediaId[(string)$candidate->expediaPropertyId] ?? null;
            if ($aresId !== null) {
                $candidate->setAresHotelId($aresId);
            }
        }
    }
}
