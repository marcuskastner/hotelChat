<?php

namespace App\Entity;

class HotelCandidate
{
    public int $expediaPropertyId {
        get {
            return $this->expediaPropertyId;
        }
        set {
            $this->expediaPropertyId = $value;
        }
    }
    private ?int $aresHotelId = null;

    public function getAresHotelId(): ?int
    {
        return $this->aresHotelId;
    }

    public function setAresHotelId(int $aresHotelId): void
    {
        $this->aresHotelId = $aresHotelId;
    }
}
