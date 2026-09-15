<?php

namespace App\Entity;

use App\Repository\HotelToChannelSourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HotelToChannelSourceRepository::class)]
#[ORM\Table(name: 'hotelToChannelSource')]
class HotelToChannelSource
{
    public const CHANNEL_NAME_EXPEDIA = 'EXPEDIA';

    #[ORM\Id]
    #[ORM\Column(name: 'channelHotelID', length: 255)]
    private string $channelHotelID;

    #[ORM\Id]
    #[ORM\Column(name: 'channelName', length: 255)]
    private string $channelName;

    #[ORM\Column(name: 'hotelID')]
    private int $hotelID;

    public function getChannelHotelID(): string
    {
        return $this->channelHotelID;
    }

    public function getHotelID(): int
    {
        return $this->hotelID;
    }

    /**
     * @api used in tests
     */
    public function setChannelHotelID(string $channelHotelID): void
    {
        $this->channelHotelID = $channelHotelID;
    }

    public function setChannelName(string $channelName): void
    {
        $this->channelName = $channelName;
    }

    public function setHotelID(int $hotelID): void
    {
        $this->hotelID = $hotelID;
    }
}
