<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{
    public function index(Request $request)
    {
        return DB::select("
            select id, title, match (content) against('" . $request->search_term . "*' in natural language mode) as relevance
            from episodes
            where match (content) against('" . $request->search_term . "*' in natural language mode)
            order by relevance desc
        ");
    }

    public function question(Request $request, Podcast $podcast, Episode $episode, OpenAiService $openAi)
    {
        return $openAi->answer($request->question, $episode->content);
    }

    public function recommendations(Episode $episode, OpenAiService $openAi)
    {
        $episodes = Episode::query()
            ->select('id', 'title', 'embeddings')
            ->whereNot('id', $episode->id)
            ->whereNotNull('embeddings')
            ->get();

        $similarEpisodeIds = $openAi->getBestMatches($episode->getEmbeddings(), $episodes, 3);

        return Episode::query()
            ->select('id', 'title')
            ->whereIn('id', $similarEpisodeIds)
            ->get();
    }
}
