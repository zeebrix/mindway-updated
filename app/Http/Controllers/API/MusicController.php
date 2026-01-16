<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Music;

class MusicController extends Controller
{
    public function getMusic()
    {
        $getMusic = Music::get();

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Music fetched successfully!", 'data' => $getMusic]);
    }
}
