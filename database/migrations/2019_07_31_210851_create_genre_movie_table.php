<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('genre_movie', function (Blueprint $table): void {
            $table->unsignedInteger('genre_id');
            $table->unsignedInteger('movie_id');
            $table->primary(['genre_id', 'movie_id']);
        });
    }
};
