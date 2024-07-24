<?php

use App\Jobs\CheckTranscribedEpisodesJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CheckTranscribedEpisodesJob())->everyThirtySeconds();
