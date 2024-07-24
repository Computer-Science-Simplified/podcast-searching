<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\OpenAiService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class TranscribeEpisodeChunkJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        private Episode $episode,
        private int $chunkNumber,
        private string $chunkFilename,
    ) {
    }

    public function handle(OpenAiService $openAi): void
    {
        $content = $openAi->transcribe($this->chunkFilename);

        Redis::hset(
            $this->episode->podcast->id . ':' . $this->episode->id . ':chunks',
            $this->chunkNumber,
            $content,
        );
    }
}
