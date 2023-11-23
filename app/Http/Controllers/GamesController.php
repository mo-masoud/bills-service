<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GamesController extends Controller
{
    public function index(): JsonResponse
    {
        $games = Game::select('id', 'name', 'image')->latest()->get();
        return response()->json($games);
    }

    public function show(Game $game): JsonResponse
    {
        return response()->json($game);
    }

    public function showDetails(Game $game): JsonResponse
    {
        return response()->json(new GameResource($game));
    }
}
