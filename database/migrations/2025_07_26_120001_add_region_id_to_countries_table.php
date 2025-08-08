<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('world_countries', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable()->after('flag');
            $table->foreign('region_id')->references('id')->on('world_regions')->onDelete('set null');
            $table->index('region_id');
        });
    }

    public function down(): void
    {
        Schema::table('world_countries', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropIndex(['region_id']);
            $table->dropColumn('region_id');
        });
    }
};
