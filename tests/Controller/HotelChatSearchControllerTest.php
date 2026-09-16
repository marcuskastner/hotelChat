<?php

namespace App\Tests\Controller;

use App\Action\HotelCandidatesFromChatSearchAction;
use App\Entity\HotelCandidate;
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

        $return = [
            new HotelCandidate()
        ];

        $action = $this->createMock(HotelCandidatesFromChatSearchAction::class);
        $action->expects($this->once())
            ->method('getHotelCandidates')
            ->with($message)
            ->willReturn($return);
        static::getContainer()->set(HotelCandidatesFromChatSearchAction::class, $action);

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

        $action = $this->createMock(HotelCandidatesFromChatSearchAction::class);
        $action->expects($this->once())
            ->method('getHotelCandidates')
            ->willThrowException(new \InvalidArgumentException('Location not found for city: Nowhere, state: OR'));
        static::getContainer()->set(HotelCandidatesFromChatSearchAction::class, $action);

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
