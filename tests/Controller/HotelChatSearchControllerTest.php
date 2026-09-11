<?php

namespace App\Tests\Controller;

use App\Tests\WebTestCase;
use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Agent\MockAgent;

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
        $agent = new MockAgent([
            $message => json_encode([
                'destination' => 'Portland',
                'checkIn' => null,
                'checkOut' => null,
                'adults' => 1,
                'rooms' => 1,
                'amenities' => [],
            ], \JSON_THROW_ON_ERROR),
        ]);
        static::getContainer()->set(AgentInterface::class, $agent);

        $client->request('POST', '/hotel/chat/search', [
            'message' => $message,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains(
            'body',
            'Find me a hotel in Portland'
        );

        $this->assertSelectorTextContains(
            'body',
            'Portland'
        );

        $agent->assertCalledWith($message);
        $agent->assertCallCount(1);
    }
}
