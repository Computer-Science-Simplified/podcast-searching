<?php

namespace App\Services;

use App\Models\HasEmbeddings;
use Illuminate\Support\Collection;
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

    /**
     * @param string $question
     * @param Collection<HasEmbeddings> $models
     * @return Collection<int|string> IDs of best matches
     */
    public function getBestMatches(string $question, Collection $models, int $numberOfMatches = 3): Collection
    {
        $questionEmbedding = $this->createEmbeddings($question);

        $distances = $this->getDistances($models, $questionEmbedding);

        return collect($distances)
            ->sortByDesc('distance')
            ->take($numberOfMatches)
            ->pluck('entity_id');
    }

    /**
     * @param Collection<HasEmbeddings> $models
     */
    private function getDistances(Collection $models, array $inputEmbedding): array
    {
        $distances = [];

        foreach ($models as $model) {
            /** @var HasEmbeddings $model */
            $distances[] = [
                'entity_id' => $model->getId(),
                'distance' => SimilarityService::cosine($inputEmbedding, $model->getEmbeddings()),
            ];
        }

        return $distances;
    }

    public function answer(string $question, string $context): ?string
    {
        $response = $this->client->completions()->create([
            'prompt' => "Answer the question based on the podcast episode below. \n\n Podcast episode: $context \n\n---\n\n Question: $question\nAnswer:",
            'model' => 'gpt-3.5-turbo-instruct',
        ]);

        if (empty($response->choices)) {
            return null;
        }

        return $response->choices[0]->text;
    }
}
