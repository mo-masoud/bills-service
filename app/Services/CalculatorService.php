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
        $skill = Skill::with('skillRanges')->findOrFail($skillId);
        $boostMethod = BootMethod::find($boostMethodId);

        $homeContent = HomeContent::first();

        // Define the level ranges and their corresponding XP tables

        $xpTables = [];
        foreach ($skill->skillRanges as $range) {
            $xpTables['gpxp_' . $range->min . '_' . $range->max] = range($range->min == 1 ? $range->min : $range->min - 1, $range->max);
        }

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

                preg_match_all('/\d+/', $table, $matches);

                $minRange = $matches[0][0];
                $maxRange = $matches[0][1];

                $gpXP = $skill->skillRanges->where('min', '>=', $minRange)->where('max', '<=', $maxRange)->first()->gp_xp;

                $totalPoints += $points;

                $goldPerGroup[$table] = [
                    'min' => $min,
                    'max' => $max,
                    'min_level' => $minRange,
                    'max_level' => $maxRange,
                    'gpXP' => $gpXP,
                    'points' => $points,
                    'gold' => ($gpXP * $points) / 1000000,
                    'price' => ($gpXP * $points) / 1000000 * $goldPerUsd,
                ];
                // $goldPerGroup[$table] = ($gpXP * $points) / 1000000;
                $gold += ($gpXP * $points) / 1000000;
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
            'goldPerGroup' => $goldPerGroup,
        ];
    }
}
