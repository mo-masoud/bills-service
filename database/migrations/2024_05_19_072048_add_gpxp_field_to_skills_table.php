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
        Schema::table('skills', function (Blueprint $table) {
            $table->integer('gpxp_1_40')->default(0);
            $table->integer('gpxp_41_60')->default(0);
            $table->integer('gpxp_61_90')->default(0);
            $table->integer('gpxp_91_99')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('gpxp_1_40');
            $table->dropColumn('gpxp_41_60');
            $table->dropColumn('gpxp_61_90');
            $table->dropColumn('gpxp_91_99');
        });
    }
};
