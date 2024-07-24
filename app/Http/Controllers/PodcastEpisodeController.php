<?php

namespace App\Http\Controllers;

use App\Jobs\ChunkEpisodeJob;
use App\Jobs\CreateEmbeddingsForEpisodeJob;
use App\Jobs\SummarizeEpisodeJob;
use App\Jobs\TranscribeEpisodeJob;
use App\Jobs\UploadEpisodeFileJob;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class PodcastEpisodeController extends Controller
{
    public function store(Request $request, Podcast $podcast)
    {
        /** @var Episode $episode */
        $episode = Episode::create([
            'podcast_id' => $podcast->id,
            'title' => $request->title,
        ]);

        $path = 'podcasts' . DIRECTORY_SEPARATOR . $episode->podcast->id . DIRECTORY_SEPARATOR . $episode->id;

        Storage::createDirectory($path);

        $episode->audio_file_path = storage_path('app' . DIRECTORY_SEPARATOR . $request->file('audio_file')->store($path));

        $episode->save();

        Bus::batch([
            [
                new ChunkEpisodeJob($episode->refresh()),
            ],
        ])
            ->dispatch();

//        Bus::batch([
//            [
//                new TranscribeEpisodeJob($episode),
//            ],
//            [
//                new SummarizeEpisodeJob($episode->refresh()),
//                new CreateEmbeddingsForEpisodeJob($episode->refresh()),
//            ],
//        ])
//            ->dispatch();

        return $episode;
    }
}
