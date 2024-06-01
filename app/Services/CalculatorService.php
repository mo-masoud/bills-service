<?php

namespace App\Services;

use App\Models\BootMethod;
use App\Models\HomeContent;
use App\Models\PointsPerLevel;
use App\Models\Skill;

class CalculatorService
{

    private static function rangesOverlap($range, $min_level, $max_level)
    {
        return max($range) > $min_level && min($range) <= $max_level;
    }


    public static function calculate($skillId, $boostMethodId, $minLevel, $maxLevel, $express, $quantity = 1)
    {
        $skill = Skill::findOrFail($skillId);
        $boostMethod = BootMethod::find($boostMethodId);

        $homeContent = HomeContent::first();

        // Define the level ranges and their corresponding XP tables
        $xpTables = [
            'gpxp_1_40' => range(1, 40),
            'gpxp_41_60' => range(40, 60),
            'gpxp_61_90' => range(60, 90),
            'gpxp_91_99' => range(90, 99),
        ];

        // Initialize an array to hold the tables that fall within the given range
        $selectedTables = [];

        $gold = 0;

        $goldPerGroup = [];
        $totalPoints = 0;

        $goldPerUsd = $homeContent->gold_per_usd ?? 0.18;

        // Iterate over each XP table and check if it falls within the given range
        foreach ($xpTables as $table => $range) {
            if (self::rangesOverlap($range, $minLevel, $maxLevel)) {

                $min = max([$minLevel, $range[0]]);
                $max = min([$maxLevel, last($range)]);

                $points = PointsPerLevel::getPoints($min, $max);
                $totalPoints += $points;
                $goldPerGroup[$table] = ($skill->$table * $points) / 1000000;
                $gold += ($skill->$table * $points) / 1000000;
                $selectedTables[] = $table;
            }
        }

        $price = $gold * $goldPerUsd;

        if ($boostMethod) {
            $price += $boostMethod->price;
        }

        if ($express == 1) {
            $price = $price + ($price * 0.4);
        }

        $price = $price * $quantity;

        return [
            'gold' => $gold,
            'goldPerUsd' => $goldPerUsd,
            'price' => round($price, 2),
            'totalPoints' => $totalPoints,
        ];
    }
}
