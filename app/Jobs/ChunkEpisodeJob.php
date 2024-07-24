<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\FileService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class ChunkEpisodeJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(private Episode $episode)
    {
    }

    public function handle(FileService $fileService): void
    {
        $fileService->chunk(
            $this->episode->audio_file_path,
            Str::beforeLast($this->episode->audio_file_path, DIRECTORY_SEPARATOR),
        );
    }
}
