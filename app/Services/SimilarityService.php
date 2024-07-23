<?php

namespace App\Services;

class SimilarityService
{
    public static function cosine(array $a, array $b): float
    {
        $dotProduct = self::dotProduct($a, $b);

        $sqrt = sqrt(self::dotProduct($a, $a) * self::dotProduct($b, $b));

        return $dotProduct / $sqrt;
    }

    private static function dotProduct(array $a, array $b): float
    {
        $products = array_map(function ($a, $b) {
            return $a * $b;
        }, $a, $b);

        return array_reduce($products, function ($a, $b) {
            return $a + $b;
        });
    }
}
