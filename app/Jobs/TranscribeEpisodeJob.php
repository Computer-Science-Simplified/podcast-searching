<?php

namespace App\Jobs;

use App\Models\Episode;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;

class TranscribeEpisodeJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(private Episode $episode)
    {
    }

    public function handle(): void
    {
        $files = collect(scandir($this->episode->chunk_folder_path))
            ->filter(fn (string $filename) => !in_array($filename, ['.', '..']))
            ->map(fn (string $filename) => $this->episode->chunk_folder_path . DIRECTORY_SEPARATOR . $filename)
            ->values();

        $jobs = [];

        foreach ($files as $i => $file) {
            $jobs[] = new TranscribeEpisodeChunkJob($this->episode, $i, $file);
        }

        $updateContent = function (Episode $episode) {
            $keys = collect(Redis::hkeys($episode->podcast->id . ':' . $episode->id . ':chunks'))
                ->sort();

            foreach ($keys as $key) {
                $content = Redis::hget(
                    $episode->podcast->id . ':' . $episode->id . ':chunks',
                    $key,
                );

                $episode->content = $episode->content . $content;

                $episode->save();
            }

            Redis::del($episode->podcast->id . ':' . $episode->id . ':chunks');

            Redis::rpush('transcribed-episodes', $episode->id);
        };

        $episode = $this->episode;

        Bus::batch($jobs)
            ->finally(fn () => $updateContent($episode))
            ->dispatch();
    }
}
