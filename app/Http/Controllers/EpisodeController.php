<?php

namespace App\Http\Controllers;

use App\Jobs\AnswerQuestionJob;
use App\Models\Episode;
use App\Models\Podcast;
use App\Services\OpenAiService;
use App\Services\SimilarityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

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

    public function question(Request $request, Episode $episode, OpenAiService $openAi)
    {
        return response([
            'data' => $openAi->answer($request->question, $episode),
        ], Response::HTTP_ACCEPTED);
    }

    public function recommendations(Episode $episode, SimilarityService $similarityService)
    {
        $episodes = Episode::query()
            ->select('id', 'title', 'embeddings')
            ->whereNot('id', $episode->id)
            ->whereNotNull('embeddings')
            ->get();

        $similarEpisodeIds = $similarityService
            ->getMostSimilarModels($episode->getEmbeddings(), $episodes, 3);

        return Episode::query()
            ->select('id', 'title')
            ->whereIn('id', $similarEpisodeIds)
            ->get();
    }
}
