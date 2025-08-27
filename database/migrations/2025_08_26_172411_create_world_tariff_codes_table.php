<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('world_tariff_codes', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('year')->index();
            $table->string('code', 20)->index();
            $table->json('name');
            $table->json('measure_unit')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['year', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('world_tariff_codes');
    }
};
