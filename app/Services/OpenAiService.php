<?php

namespace App\Services;

use Illuminate\Support\Str;
use OpenAI;
use OpenAI\Client;

class OpenAiService
{
    private Client $client;

    public function __construct(
        private string $apiKey,
    ) {
        $this->client = OpenAI::client($this->apiKey);
    }

    public function transcribe(string $filePath): ?string
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
            'input' => Str::substr($text, 0, 30_000),
        ]);

        return $response->embeddings[0]->embedding;
    }

    public function answer(string $question, string $context): ?string
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Based on the context above answer the question. Context: $context. Question: $question",
                ],
            ],
        ]);

        if (empty($response->choices)) {
            return null;
        }

        return $response->choices[0]->message->content;
    }
}
