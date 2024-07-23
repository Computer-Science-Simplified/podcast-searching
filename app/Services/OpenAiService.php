<?php

namespace App\Services;

use OpenAI;
use OpenAI\Client;

class OpenAiService
{
    private Client $client;

    public function __construct(private string $apiKey)
    {
        $this->client = OpenAI::client($this->apiKey);
    }

    public function transcript(string $filePath): string
    {
        $response = $this->client->audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($filePath, 'r'),
        ]);

        return $response->text;
    }

    public function summarize(string $text): ?string
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'user', 'content' => 'Summarize the following text in 5-8 sentences. Text: "' . $text . '"'],
            ],
        ]);

        if (empty($response->choices)) {
            return null;
        }

        return $response->choices[0]->message->content;
    }

    public function createEmbeddings(string $text): array
    {
        $response = $this->client->embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }
}
