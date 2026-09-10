<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;

class HotelChatControllerTest extends WebTestCase
{
    public function testChatPageLoads(): void
    {
        $client = static::createClient();

        $client->request('GET', '/hotel/chat');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Hotel Search');
        self::assertSelectorExists('input[name="message"]');
        self::assertSelectorExists('button[type="submit"]');
    }
}
