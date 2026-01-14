<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function __construct(private CourseService $service) {}

    public function index(Request $request)
    {
        $courses = $this->service->getCoursesForUser($request->user_id);
        $courses = $courses ? collect($courses) : collect([]);
        $data = CourseResource::collection($courses);
        return response()->json(['data' => $data, 'status' => true]);
    }

    public function show(Request $request, $id)
    {
        $course = $this->service->getCourseDetail($id, $request->user_id);
        return new CourseDetailResource($course);
    }
}
