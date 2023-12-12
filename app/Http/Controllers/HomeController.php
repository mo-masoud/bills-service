<?php

namespace App\Http\Controllers;

use App\Models\HomeContent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $home = HomeContent::first();
        if (!$home) {
            return response()->json(['message' => 'please fill your home content from dashboard'], 400);
        }

        return response()->json([
            'title' => $home->title,
            'body' => $home->body,
            'video' => $home->video,
        ]);
    }
}
