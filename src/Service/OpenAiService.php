<?php

namespace App\Service;

use Symfony\AI\Agent\AgentInterface;

final readonly class OpenAiService
{
    public function __construct(
        private AgentInterface $agent,
    ) {
    }

    public function ask(string $message): array
    {
        $content = $this->agent->call($message)->asText();
        return json_decode($content, true);
    }
}
