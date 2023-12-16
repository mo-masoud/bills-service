<?php

use App\Models\BootMethod;
use App\Models\Game;
use App\Models\Order;
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
        Schema::create('order_power_level_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Game::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Skill::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(BootMethod::class)->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('current_level');
            $table->unsignedInteger('desired_level');
            $table->unsignedDouble('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_power_level_items');
    }
};
