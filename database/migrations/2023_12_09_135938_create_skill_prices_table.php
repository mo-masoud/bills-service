<?php

use App\Models\Skill;
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
        Schema::create('skill_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Skill::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('min_level');
            $table->unsignedInteger('max_level');
            $table->double('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_prices');
    }
};
