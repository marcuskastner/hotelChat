<?php

namespace App\Entity;

class RankingImportance
{
    public const string NA = 'NA';
    public const string LOW = 'LOW';
    public const string MEDIUM = 'MEDIUM';
    public const string HIGH = 'HIGH';

    public const array VALUES = [
        self::NA,
        self::LOW,
        self::MEDIUM,
        self::HIGH,
    ];

    public const array WEIGHTS = [
        self::NA => 0.0,
        self::LOW => 1.0,
        self::MEDIUM => 2.0,
        self::HIGH => 3.0,
    ];

    public static function weight(string $importance): float
    {
        return self::WEIGHTS[$importance] ?? 0.0;
    }
}
