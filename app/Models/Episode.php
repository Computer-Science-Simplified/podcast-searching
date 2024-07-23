<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model implements HasEmbeddings
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'embeddings' => 'array',
    ];

    public function getEmbeddings(): array
    {
        return $this->embeddings;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
