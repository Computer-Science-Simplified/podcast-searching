<?php

namespace App\Jobs;

use App\Models\Episode;
use App\Services\OpenAiService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SummarizeEpisodeJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(private Episode $episode)
    {
    }

    public function handle(OpenAiService $openAi): void
    {
        $this->episode->summary = $openAi->summarize($this->episode->content);

        $this->episode->save();
    }
}
