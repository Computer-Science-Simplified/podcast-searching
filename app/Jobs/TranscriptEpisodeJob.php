<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\OpenAiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TranscriptEpisodeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Episode $episode)
    {
    }

    public function handle(OpenAiService $openAi): void
    {
        $this->episode->content = $openAi->transcript($this->episode);

        $this->episode->summary = $openAi->summarize($this->episode->content);

        $this->episode->embeddings = $openAi->createEmbeddings($this->episode->content);

        $this->episode->save();
    }
}
