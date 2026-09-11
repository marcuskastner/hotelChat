<?php

namespace App\Service;

use App\Entity\HotelSearch;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HotelSearchValidator
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    public function validate(HotelSearch $search): void
    {
        $violations = $this->validator->validate($search);

        if (count($violations) > 0) {
            throw new \InvalidArgumentException(
                (string) $violations
            );
        }
    }
}
