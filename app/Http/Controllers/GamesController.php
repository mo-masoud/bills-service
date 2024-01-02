<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\JsonResponse;

class GamesController extends Controller
{
    public function index(): JsonResponse
    {
        $games = Game::whereHas('powerlevel')->select('id', 'name', 'image')->latest()->get();
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

    public function quests(Game $game): JsonResponse
    {
        $quests = $game->quests()
            ->when(request('search'), fn($q) => $q->where('name', 'like', '%'.request('search').'%'))
            ->paginate();
        return response()->json($quests);
    }

    public function services(Game $game): JsonResponse
    {
        return response()->json($game->services);
    }
}
