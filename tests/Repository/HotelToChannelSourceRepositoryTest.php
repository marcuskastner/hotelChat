<?php

namespace App\Tests\Repository;

use App\Entity\HotelCandidate;
use App\Entity\HotelToChannelSource;
use App\Repository\HotelToChannelSourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HotelToChannelSourceRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private HotelToChannelSourceRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $metadata = [$this->entityManager->getClassMetadata(HotelToChannelSource::class)];
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $this->repository = static::getContainer()->get(HotelToChannelSourceRepository::class);
    }

    public function testDecorateAssignsMatchingExpediaIds(): void
    {
        $this->persistSource('1001', HotelToChannelSource::CHANNEL_NAME_EXPEDIA, 42);
        $this->persistSource('1001', 'BOOKING', 99);
        $this->persistSource('1002', HotelToChannelSource::CHANNEL_NAME_EXPEDIA, 7);

        $matched = $this->candidate(1001);
        $unmatched = $this->candidate(1003);

        $this->repository->decorateAresHotelIds([$matched, $unmatched]);

        self::assertSame(42, $matched->getAresHotelId());
        self::assertNull($unmatched->getAresHotelId());
    }

    public function testDecorateIgnoresAnEmptyCandidateList(): void
    {
        $this->persistSource('1001', HotelToChannelSource::CHANNEL_NAME_EXPEDIA, 42);

        $this->repository->decorateAresHotelIds([]);

        self::assertSame(42, $this->repository->findOneBy([
            'channelName' => HotelToChannelSource::CHANNEL_NAME_EXPEDIA,
            'channelHotelID' => '1001',
        ])?->getHotelID());
    }

    private function persistSource(string $channelHotelId, string $channelName, int $hotelId): void
    {
        $source = new HotelToChannelSource();
        $source->setChannelHotelID($channelHotelId);
        $source->setChannelName($channelName);
        $source->setHotelID($hotelId);

        $this->entityManager->persist($source);
        $this->entityManager->flush();
    }

    private function candidate(int $expediaPropertyId): HotelCandidate
    {
        $candidate = new HotelCandidate();
        $candidate->expediaPropertyId = $expediaPropertyId;

        return $candidate;
    }
}
