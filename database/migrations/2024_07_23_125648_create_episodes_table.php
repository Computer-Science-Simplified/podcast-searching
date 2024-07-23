<?php

use App\Models\Podcast;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Podcast::class)->constrained();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->mediumText('content')->nullable();
            $table->string('audio_file_path');
            $table->json('embeddings')->nullable();
            $table->timestamps();

            $table->fullText(['title', 'summary', 'content']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
