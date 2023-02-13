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
        Schema::create('person_tv', function (Blueprint $table): void {
            $table->unsignedInteger('person_id');
            $table->unsignedInteger('tv_id');
            $table->primary(['person_id', 'tv_id']);
        });
    }
};
