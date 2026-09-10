<?php

namespace App\Tests\Controller;

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

        $client->request('POST', '/hotel/chat/search', [
            'message' => 'Find me a hotel in Portland',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            'Find me a hotel in Portland'
        );
    }
}
