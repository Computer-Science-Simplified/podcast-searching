<?php

namespace App\Services;

use App\Models\HasEmbeddings;
use Illuminate\Support\Collection;

class SimilarityService
{
    /**
     * @param Collection<HasEmbeddings> $models
     * @return Collection<int|string> IDs of best matches
     */
    public function getMostSimilarModels(array $embeddings, Collection $models, int $numberOfMatches = 3): Collection
    {
        return $this->getSimilarities($models, $embeddings)
            ->sortByDesc('similarity')
            ->take($numberOfMatches)
            ->pluck('model_id');
    }

    /**
     * @param Collection<HasEmbeddings> $models
     * @return Collection<array<string, mixed>>
     */
    private function getSimilarities(Collection $models, array $inputEmbedding): Collection
    {
        $similarities = [];

        foreach ($models as $model) {
            /** @var HasEmbeddings $model */
            $similarities[] = [
                'model_id' => $model->getId(),
                'similarity' => $this->cosineSimilarity($inputEmbedding, $model->getEmbeddings()),
            ];
        }

        return collect($similarities);
    }

    public function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = $this->dotProduct($a, $b);

        $magnitudeA = $this->magnitude($a);

        $magnitudeB = $this->magnitude($b);

        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    private function dotProduct(array $a, array $b): float
    {
        $products = array_map(fn ($ax, $bx) => $ax * $bx, $a, $b);

        return array_sum($products);
    }

    private function magnitude(array $a): float
    {
        return sqrt(array_sum(array_map(fn ($x) => $x * $x, $a)));
    }
}
