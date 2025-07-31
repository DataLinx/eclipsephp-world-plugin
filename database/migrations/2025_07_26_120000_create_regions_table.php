<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('world_regions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_special')->default(false);
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('world_regions')->onDelete('cascade');
            $table->index(['is_special', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('world_regions');
    }
};
