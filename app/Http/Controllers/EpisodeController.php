<?php

namespace App\Http\Controllers;

use App\Jobs\TranscriptEpisodeJob;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{
    public function index(Request $request)
    {
        $results = DB::select("
            select id, title
            from episodes
            where match (title, summary, content) against('" . $request->search_term . "' in natural language mode)
        ");

        return $results;
    }

    }
