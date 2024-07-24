<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Episode extends Model implements HasEmbeddings
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'embeddings' => 'array',
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

    public function getEmbeddings(): array
    {
        return $this->embeddings;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function chunkFolderPath(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::beforeLast($this->audio_file_path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'chunks',
        );
    }
}
