<?php

namespace App\Http\Controllers;

use App\Jobs\TranscriptEpisodeJob;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;

class PodcastEpisodeController extends Controller
{
    public function store(Request $request, Podcast $podcast)
    {
        $episode = Episode::create([
            'podcast_id' => $podcast->id,
            'audio_file_path' => storage_path('app/sample.mp3'),
            'title' => $request->title,
        ]);

        TranscriptEpisodeJob::dispatch($episode);

        return $episode;
    }
}
