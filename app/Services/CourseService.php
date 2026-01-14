<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseProgress;
use App\Models\LessonProgress;

class CourseService
{
    public function getCoursesForUser(int $userId)
    {
        return Course::withCount('lessons')
            ->with(['progress' => fn($q) => $q->where('user_id', $userId)])
            ->get();
    }

    public function getCourseDetail(int $courseId, int $userId)
    {
        return Course::with(['progress' => fn($q) => $q->where('user_id', $userId)])->with([
            'lessons' => function ($q) use ($userId) {
                $q->orderBy('order_no')
                    ->with([
                        'media',
                        'progress' => function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        }
                    ]);
            }
        ])->findOrFail($courseId);
    }

    public function markLessonComplete(int $lessonId, int $userId)
    {
        LessonProgress::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lessonId],
            ['is_completed' => true, 'completed_at' => now()]
        );

        $lesson = CourseLesson::findOrFail($lessonId);
        $course = $lesson->course;

        $total = $course->lessons()->count();
        $completed = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons()->pluck('id'))
            ->count();

        $percent = intval(($completed / $total) * 100);

        CourseProgress::updateOrCreate(
            ['user_id' => $userId, 'course_id' => $course->id],
            [
                'last_lesson_id' => $lessonId,
                'progress_percent' => $percent,
                'completed_at' => $percent === 100 ? now() : null
            ]
        );

        return $percent;
    }
}
