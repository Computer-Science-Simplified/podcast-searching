<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\PodcastEpisodeController;
use Illuminate\Support\Facades\Route;

Route::get('/episodes', [EpisodeController::class, 'index']);

Route::get('/episodes/{episode}/recommendations', [EpisodeController::class, 'recommendations']);

Route::get('/episodes/{episode}/question', [EpisodeController::class, 'question']);

Route::post('/podcasts/{podcast}/episodes', [PodcastEpisodeController::class, 'store']);
