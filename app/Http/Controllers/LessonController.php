<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\CourseLesson;
use App\Services\CourseService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show(Request $request, $id)
    {
        $lesson = CourseLesson::with([
        'media',
        'progress' => function ($query) use ($request) {
            $query->where('user_id', $request->user_id);
        }
    ])->findOrFail($id);
        return new LessonResource($lesson);
    }

    public function complete(Request $request, $id, CourseService $service)
    {
        $progress = $service->markLessonComplete($id, $request->user_id);

        return response()->json([
            'success' => true,
            'course_progress' => $progress,
        ]);
    }
}
