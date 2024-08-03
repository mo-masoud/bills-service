<?php

namespace App\Http\Controllers;

use App\Models\ServiceOption;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json([
            [
                'key' => 'fire-cape',
                'name' => 'Fire Cape',
            ],
            [
                'key' => 'infernal-cape',
                'name' => 'Infernal Cape',
            ],
            [
                'key' => 'fortis-colosseum',
                'name' => 'Fortis Colosseum',
            ],
            [
                'key' => 'minigames',
                'name' => 'Minigames',
            ],
            [
                'key' => 'raids',
                'name' => 'Raids',
            ],
            [
                'key' => 'pvm-bossing',
                'name' => 'PvM | Bossing',
            ],
            [
                'key' => 'combat-achievements',
                'name' => 'Combat Achievements',
            ],
            [
                'key' => 'achievement-diary',
                'name' => 'Achievement Diary',
            ],
            [
                'key' => 'ironman-collecting',
                'name' => 'Ironman Collecting',
            ],
        ]);
    }

    public function show($service)
    {

        if ($service === 'minigames') {
            $options = ServiceOption::with('children.children.children')
                ->whereNull('parent_id')
                ->where('service', 'minigame')
                ->get()
                ->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'name' => $option->name,
                        'has_quantity' => $option->has_quantity,
                        'children' => $option->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'price' => $child->price,
                                'type' => $child->type,
                                'children' => $child->children->map(function ($child) {
                                    return [
                                        'id' => $child->id,
                                        'name' => $child->name,
                                        'price' => $child->price,
                                        'type' => $child->type,
                                        'children' => $child->children->map(function ($child) {
                                            return [
                                                'id' => $child->id,
                                                'name' => $child->name,
                                                'price' => $child->price,
                                                'type' => $child->type,
                                            ];
                                        }),
                                    ];
                                }),
                            ];
                        }),
                    ];
                });

            return response()->json($options);
        }

        $options = ServiceOption::with('children.children')
            ->whereNull('parent_id')
            ->where('service', $service)
            ->get()
            ->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'has_quantity' => $option->has_quantity,
                    'children' => $option->children->map(function ($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'price' => $child->price,
                            'type' => $child->type,
                            'children' => $child->children->map(function ($child) {
                                return [
                                    'id' => $child->id,
                                    'name' => $child->name,
                                    'price' => $child->price,
                                    'type' => $child->type,
                                ];
                            }),
                        ];
                    }),
                ];
            });

        return response()->json($options);
    }
}
