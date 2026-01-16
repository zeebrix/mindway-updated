<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feeling;
use App\Models\Music;

class FeelingController extends Controller
{
    public function getEmoji()
    {
        $getMusic = Feeling::get();

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Music fetched successfully!", 'data' => $getMusic]);
    }
}
