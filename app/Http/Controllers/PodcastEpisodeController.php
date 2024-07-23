<?php

namespace App\Http\Controllers;

use App\Jobs\CreateEmbeddingsForEpisodeJob;
use App\Jobs\SummarizeEpisodeJob;
use App\Jobs\TranscribeEpisodeJob;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class PodcastEpisodeController extends Controller
{
    public function store(Request $request, Podcast $podcast)
    {
        /** @var Episode $episode */
        $episode = Episode::create([
            'podcast_id' => $podcast->id,
            'audio_file_path' => storage_path('app/sample.mp3'),
            'title' => $request->title,
        ]);

        Bus::batch([
            [
                new TranscribeEpisodeJob($episode),
            ],
            [
                new SummarizeEpisodeJob($episode->refresh()),
                new CreateEmbeddingsForEpisodeJob($episode->refresh()),
            ],
        ])
            ->dispatch();

        return $episode;
    }
}
