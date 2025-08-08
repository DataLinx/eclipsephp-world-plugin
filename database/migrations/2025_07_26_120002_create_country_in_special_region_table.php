<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('world_country_in_special_region', function (Blueprint $table) {
            $table->id();
            $table->string('country_id', 2);
            $table->unsignedBigInteger('region_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('world_countries')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('world_regions')->onDelete('cascade');

            $table->unique(['country_id', 'region_id', 'start_date'], 'unique_country_region_start');
            $table->index(['region_id', 'start_date', 'end_date'], 'idx_region_dates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('world_country_in_special_region');
    }
};
