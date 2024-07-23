<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\PodcastEpisodeController;
use Illuminate\Support\Facades\Route;

Route::get('/episodes', [EpisodeController::class, 'index']);

Route::post('/podcasts/{podcast}/episodes', [PodcastEpisodeController::class, 'store']);

Route::get('/podcasts/{podcast}/episodes/{episode}/question', [PodcastEpisodeController::class, 'question']);
