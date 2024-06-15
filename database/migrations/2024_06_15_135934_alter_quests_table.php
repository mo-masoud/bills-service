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
        Schema::table('quests', function (Blueprint $table) {
            $table->string('difficulty');
            $table->unsignedDouble('price', 8, 2);

            $table->dropColumn('easy_price');
            $table->dropColumn('medium_price');
            $table->dropColumn('hard_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn('difficulty');
            $table->dropColumn('price');

            $table->unsignedDouble('easy_price', 8, 2);
            $table->unsignedDouble('medium_price', 8, 2);
            $table->unsignedDouble('hard_price', 8, 2);
        });
    }
};
