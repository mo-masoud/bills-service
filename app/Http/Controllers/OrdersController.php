<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            OrderResource::collection(
                $request->user()
                    ->orders()
                    ->with('powerlevelItems')
                    ->get()
            )
        );
    }
}
