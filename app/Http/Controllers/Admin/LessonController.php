<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    use DataTableTrait;

    public function index($id)
    {
        $config = $this->getLessonDataTableConfig($id);

        return $this->renderDataTableView('admin.lessons.index', $config, [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Lessons', 'url' => null],
            ],
        ]);
    }

    public function getData(Request $request, $id)
    {
        $config = $this->getLessonDataTableConfig($id);
        return $this->handleDataTableRequest($request, CourseLesson::class, $config);
    }

    protected function getLessonDataTableConfig($id = 0): array
    {
        return [
            'table_id' => 'lessons-datatable',
            'title' => 'Lessons',
            'add_new_url' => route('admin.lessons.create', $id),
            'add_new_text' => 'Add New Lesson',
            'ajax_url' => route('admin.lessons.data', $id),
            'page_length' => 25,
            'columns' => [
                'id' => ['title' => 'ID'],
                'title' => ['title' => 'Lesson Title'],
                'lesson_type' => ['title' => 'Lesson Type'],
                'duration_minutes' => ['title' => 'Duration (min)'],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => [
                    'url' => fn($row) => route('admin.lessons.edit', [
                        'course' => $id,
                        'lesson' => $row->id,
                    ]),
                    'class' => 'btn btn-sm btn-warning',
                    'text' => 'Edit',
                ],
                'delete' => [
                    'url' => fn($row) => route('admin.lessons.destroy', [
                        'course' => $id,
                        'lesson' => $row->id,
                    ]),
                    'class' => 'btn btn-sm btn-danger action-delete',
                    'text' => 'Delete',
                ],
            ],
        ];
    }

    public function create($courseId)
    {
        return view('admin.lessons.create', [
            'lesson' => new CourseLesson(),
            'courseId' => $courseId
        ]);
    }

    public function store(Request $request, $course_id)
    {
        $this->validateLesson($request);

        $data = $this->prepareLessonData($request);
        $data['course_id'] = $course_id;
        CourseLesson::create($data);
        return redirect()
            ->route('admin.lessons.index', $course_id)
            ->with('success', 'Lesson created successfully.');
    }

    public function edit($course_id, CourseLesson $lesson)
    {
        return view('admin.lessons.edit', compact('lesson', 'course_id'));
    }

    public function update(Request $request, $course_id, CourseLesson $lesson)
    {
        $this->validateLesson($request);

        $data = $this->prepareLessonData($request, $lesson);
        $data['course_id'] = $course_id;

        $lesson->update($data);

        return redirect()
            ->route('admin.lessons.index', $course_id)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy($course_id, CourseLesson $lesson)
    {
        if ($lesson->audio) {
            Storage::delete($lesson->audio);
        }

        if ($lesson->video) {
            Storage::delete($lesson->video);
        }

        $lesson->delete();

        return request()->ajax()
            ? response()->json(['success' => true])
            : redirect()->route('admin.lessons.index')
            ->with('success', 'Lesson deleted successfully.');
    }

    /**
     * -----------------------------------
     * Helpers
     * -----------------------------------
     */

    protected function validateLesson(Request $request): void
    {
        Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|string|min:1',
            'lesson_type' => 'required|in:audio,video,article',
            'order_no' => 'nullable|integer',

            'audio' => 'required_if:lesson_type,audio|nullable|file|mimes:mp3,wav',
            'video' => 'required_if:lesson_type,video|nullable|file|mimes:mp4,mov,avi',
            'article_text' => 'required_if:lesson_type,article|nullable|string',

            'host_name' => 'nullable|string|max:255',
            'author_name' => 'nullable|string|max:255',
        ])->validate();
    }

    protected function prepareLessonData(Request $request, ?CourseLesson $lesson = null): array
    {
        $data = $request->only([
            'title',
            'description',
            'duration_minutes',
            'lesson_type',
            'order_no',
            'article_text',
            'host_name',
            'author_name',
        ]);

        // Reset media fields
        $data['audio'] = null;
        $data['video'] = null;
        $data['article_text'] = null;

        if ($request->lesson_type === 'audio' && $request->hasFile('audio')) {
            if ($lesson?->audio) Storage::delete($lesson->audio);
            $data['audio'] = $request->file('audio')->store('lessons/audio');
        }

        if ($request->lesson_type === 'video' && $request->hasFile('video')) {
            if ($lesson?->video) Storage::delete($lesson->video);
            $data['video'] = $request->file('video')->store('lessons/video');
        }

        if ($request->lesson_type === 'article') {
            $data['article_text'] = $request->article_text;
        }
        return $data;
    }
}
