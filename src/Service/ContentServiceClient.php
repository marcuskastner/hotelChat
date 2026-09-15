<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ContentServiceClient
{
    private HttpClientInterface $client;
    private string $baseUrl;

    public function __construct(
        HttpClientInterface $client,
        string $baseUrl
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }


    public function getExpediaPropertyIds(array $data = []): array
    {
        $response = $this->client->request(
            'POST',
            $this->baseUrl . '/hotel/expedia/geography',
            [
                'json' => $data,
            ]
        );

        return $response->toArray();
    }
}
