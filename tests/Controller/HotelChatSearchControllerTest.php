<?php

namespace App\Tests\Controller;

use App\Action\HotelCandidatesFromChatSearchAction;
use App\Action\HotelSearchFromChatAction;
use App\Entity\Hotel;
use App\Entity\HotelCandidate;
use App\Entity\HotelSearch;
use App\Service\HotelContentService;
use App\Tests\WebTestCase;

class HotelChatSearchControllerTest extends WebTestCase
{
    public function testSearchWithoutMessage(): void
    {
        $client = static::createClient();

        $client->request('POST', '/hotel/chat/search', [
            'message' => '',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            'Message is required'
        );
    }

    public function testSearchWithMessage(): void
    {
        $client = static::createClient();

        $message = 'Find me a hotel in Portland';
        $search = new HotelSearch(
            city: 'Portland',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 1,
            rooms: 1,
        );
        $candidates = [
            new HotelCandidate(),
        ];
        $hotels = [
            new Hotel(),
        ];

        $searchAction = $this->createMock(HotelSearchFromChatAction::class);
        $searchAction->expects($this->once())
            ->method('getHotelSearch')
            ->with($message)
            ->willReturn($search);
        static::getContainer()->set(HotelSearchFromChatAction::class, $searchAction);

        $candidatesAction = $this->createMock(HotelCandidatesFromChatSearchAction::class);
        $candidatesAction->expects($this->once())
            ->method('getHotelCandidates')
            ->with($search)
            ->willReturn($candidates);
        static::getContainer()->set(HotelCandidatesFromChatSearchAction::class, $candidatesAction);

        $contentService = $this->createMock(HotelContentService::class);
        $contentService->expects($this->once())
            ->method('getHotelsContent')
            ->with($search, $candidates)
            ->willReturn($hotels);
        static::getContainer()->set(HotelContentService::class, $contentService);

        $client->request('POST', '/hotel/chat/search', [
            'message' => $message,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            'Find me a hotel in Portland'
        );

        $this->assertSelectorTextContains('body', 'Portland');
        $this->assertSelectorTextContains('body', 'OR');
        $this->assertSelectorTextContains('body', '2026-09-15');
        $this->assertSelectorTextContains('body', '2026-09-17');
    }

    public function testSearchShowsAlertWhenActionFails(): void
    {
        $client = static::createClient();

        $search = new HotelSearch(
            city: 'Nowhere',
            state: 'OR',
            checkIn: '2026-09-15',
            checkOut: '2026-09-17',
            adults: 1,
            rooms: 1,
        );

        $searchAction = $this->createMock(HotelSearchFromChatAction::class);
        $searchAction->expects($this->once())
            ->method('getHotelSearch')
            ->willReturn($search);
        static::getContainer()->set(HotelSearchFromChatAction::class, $searchAction);

        $candidatesAction = $this->createMock(HotelCandidatesFromChatSearchAction::class);
        $candidatesAction->expects($this->once())
            ->method('getHotelCandidates')
            ->with($search)
            ->willThrowException(new \InvalidArgumentException('Location not found for city: Nowhere, state: OR'));
        static::getContainer()->set(HotelCandidatesFromChatSearchAction::class, $candidatesAction);

        $client->request('POST', '/hotel/chat/search', [
            'message' => 'Find me a hotel in Nowhere',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains(
            'body',
            'Location not found for city: Nowhere, state: OR'
        );
    }
}
