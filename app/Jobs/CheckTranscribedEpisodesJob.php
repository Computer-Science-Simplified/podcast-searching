<?php

namespace App\Jobs;

use App\Models\Episode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;

class CheckTranscribedEpisodesJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $episodeId = Redis::lpop('transcribed-episodes');

        if (!$episodeId) {
            return;
        }

        $episode = Episode::find($episodeId);

        Bus::batch([
            new SummarizeEpisodeJob($episode),
            new CreateEmbeddingsForEpisodeJob($episode->refresh()),
        ])
            ->catch(function () use ($episodeId) {
                Redis::rpush('transcribed-episodes', $episodeId);
            })
            ->dispatch();
    }
}
