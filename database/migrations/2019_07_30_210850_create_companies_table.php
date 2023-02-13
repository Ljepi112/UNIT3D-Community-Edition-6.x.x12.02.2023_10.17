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
        Schema::create('companies', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->mediumText('description')->nullable();
            $table->string('headquarters')->nullable();
            $table->string('homepage')->nullable();
            $table->string('logo')->nullable();
            $table->string('origin_country')->nullable();
        });
    }
};
