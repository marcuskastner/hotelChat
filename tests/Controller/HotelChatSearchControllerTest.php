<?php

namespace App\Tests\Controller;

use App\Action\HotelChatSearchAction;
use App\Entity\HotelSearch;
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
            children: 0,
            rooms: 1,
            amenities: [],
            latitude: 45.5234515,
            longitude: -122.6762071,
        );

        $action = $this->createMock(HotelChatSearchAction::class);
        $action->expects($this->once())
            ->method('execute')
            ->with($message)
            ->willReturn($search);
        static::getContainer()->set(HotelChatSearchAction::class, $action);

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

        $action = $this->createMock(HotelChatSearchAction::class);
        $action->expects($this->once())
            ->method('execute')
            ->willThrowException(new \InvalidArgumentException('Location not found for city: Nowhere, state: OR'));
        static::getContainer()->set(HotelChatSearchAction::class, $action);

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
