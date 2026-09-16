<?php

namespace App\Service;

use Symfony\AI\Agent\AgentInterface;

final readonly class OpenAiService
{
    public function __construct(
        private AgentInterface $agent,
        private ?\DateTimeImmutable $today = null,
    ) {
    }

    public function ask(string $message): array
    {
        $content = $this->agent->call($this->prefixedMessage($message))->asText();

        return json_decode($content, true);
    }

    public function prefixedMessage(string $message): string
    {
        return sprintf("Today is %s.\n\n%s", $this->today()->format('l, Y-m-d'), $message);
    }

    private function today(): \DateTimeImmutable
    {
        return $this->today ?? new \DateTimeImmutable('today');
    }
}
