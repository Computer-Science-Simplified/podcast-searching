<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\FileService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
            $this->episode->chunk_folder_path,
        );
    }
}
