<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpisodeController extends Controller
{
    public function index(Request $request)
    {
        return DB::select("
            select id, title, match (title, summary, content) against('" . $request->search_term . "*' in natural language mode) as relevance
            from episodes
            where match (title, summary, content) against('" . $request->search_term . "*' in natural language mode)
            order by relevance desc
        ");
    }
}
