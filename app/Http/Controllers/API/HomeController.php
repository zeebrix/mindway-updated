<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Feeling;
use App\Models\Quote;
use App\Models\SleepAudio;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function getHome()
    {
        $getHome = Feeling::get();
        return response()->json(['code' => 200, 'status' => "success", 'message' => "Your record fetched successfully!", 'message' => $getHome]);
    }
    public function getHomeEmoji()
    {
        $getHomeEmoji = Feeling::get();
        return response()->json(['code' => 200, 'status' => "success", 'message' => "Home emoji fetched successfully!", 'data' => $getHomeEmoji]);
    }
    public function getQuote($id, $date)
    {
        $userId = $id;
        $quoteOfDayKey = 'quote_of_day_' . $userId;
        $storedDate = Cache::get($quoteOfDayKey);

        if ($storedDate !== $date) {
            $quote = Quote::inRandomOrder()->first();
            Cache::put($quoteOfDayKey, $date, now()->endOfDay());
            Cache::put('quote_' . $userId, $quote, now()->endOfDay());
        } else {
            $quote = Cache::get('quote_' . $userId);
        }

        return response()->json([
            'code' => 200,
            'status' => "success",
            'message' => "Your record fetched successfully!",
            'data' => [$quote]
        ]);
    }
    public function getCourse($id, $course_order_by)
    {
        $course = Course::where('id', $id)->with('lesson')
            ->first();

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Single course fetched successfully!", 'data' => $course]);
    }
    public function getUser($id)
    {
        $user = User::customers()->find($id);
        return response()->json(['code' => 200, 'status' => "success", 'message' => "Your record fetched successfully!", 'data' => [$user]]);
    }
    public function getHomeSleepAudio($id, $date)
    {
        $userId = $id;
        $quoteOfDayKey = 'sleep_of_day' . $userId;
        $storedDate = Cache::get($quoteOfDayKey);

        if ($storedDate !== $date) {
            $getHomeSleepAudio = SleepAudio::inRandomOrder()->limit(1)->get();
            Cache::put($quoteOfDayKey, $date, now()->endOfDay());
            Cache::put('sleep_' . $userId, $getHomeSleepAudio, now()->endOfDay());
        } else {
            $getHomeSleepAudio = Cache::get('sleep_' . $userId);
        }

        return response()->json([
            'code' => 200,
            'status' => "success",
            'message' => "Random Audio sleep for home fetched successfully!",
            'data' => $getHomeSleepAudio
        ]);
    }
    public function getRandomCourse()
    {
        $course = Course::inRandomOrder()
            ->limit(1)
            ->first();

        return response()->json(['code' => 200, 'status' => "success", 'message' => "Rendom course fetched successfully!", 'data' => $course]);
    }
}
