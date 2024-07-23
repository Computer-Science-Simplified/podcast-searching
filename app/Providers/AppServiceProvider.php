<?php

namespace App\Providers;

use App\Services\OpenAiService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(OpenAiService::class, fn () => new OpenAiService(
            config('services.openai.api_key')
        ));
    }
}
