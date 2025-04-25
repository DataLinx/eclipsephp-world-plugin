<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('world_countries', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->constrained('world_regions')->nullOnDelete();
        });               
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
