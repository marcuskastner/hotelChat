<?php

namespace App\Tests\Service;

use App\Entity\HotelSearch;
use App\Service\HotelSearchValidator;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class HotelSearchValidatorTest extends KernelTestCase
{
    private HotelSearchValidator $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = static::getContainer()
            ->get(HotelSearchValidator::class);
    }

    public function testValidSearch(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            children: 1,
            rooms: 1,
            amenities: ['pool'],
        );

        $this->validator->validate($search);

        self::assertTrue(true);
    }

    public function testDestinationIsRequired(): void
    {
        $search = new HotelSearch(
            destination: null,
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testDestinationCannotBeBlank(): void
    {
        $search = new HotelSearch(
            destination: '',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testAdultsMustBePositive(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 0,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testNegativeAdultsAreInvalid(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: -1,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testRoomsMustBePositive(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 0,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testNegativeRoomsAreInvalid(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: -1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testCheckInDateCanBeNull(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: null,
            checkOut: null,
            adults: 2,
            rooms: 1,
        );

        $this->validator->validate($search);

        self::assertTrue(true);
    }

    public function testCheckInDateMustBeValid(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: 'not-a-date',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testCheckOutDateMustBeValid(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: 'not-a-date',
            adults: 2,
            rooms: 1,
        );

        $this->expectException(InvalidArgumentException::class);

        $this->validator->validate($search);
    }

    public function testValidSearchWithoutDates(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            adults: 2,
            rooms: 1,
            amenities: ['pool', 'wifi'],
        );

        $this->validator->validate($search);

        self::assertTrue(true);
    }

    public function testValidSearchWithMultipleAmenities(): void
    {
        $search = new HotelSearch(
            destination: 'San Diego',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 2,
            rooms: 1,
            amenities: [
                'pool',
                'wifi',
                'gym',
                'parking',
            ],
        );

        $this->validator->validate($search);

        self::assertTrue(true);
    }
}
