<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\Booking;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CounselorsController extends Controller
{
    use DataTableTrait;

    /**
     * Display a listing of the resource. (The main datatable view)
     */
    public function index()
    {
        $config = $this->getCounselorDataTableConfig();
        $path = public_path('timezones.json');
        $timezones = json_decode(File::get($path), true);
        $specialization = [
        "Stress", "Burnout", "Anxiety", "Depression", "Grief & Loss",
        "Sleep Difficulties", "Conflict Resolution", "Family & Relationship Issues",
        "Leader/Manager Support", "Addiction", "Trauma & PTSD",
        "Work-Life Balance", "Personal Development", "Career Counselling",
        "Mindfulness", "Coping Strategies", "Life Transitions", "Anger Management",
        "Confidence Building", "Parenting Support", "Sexuality & Identity Issues",
        "Workplace Bullying & Harassment", "Communication Skills",
        "Motivation & Goal Setting", "Eating Disorders", "Body Image Issues",
        "Cognitive Behavioural Therapy (CBT)", "Emotional Regulation",
        "Finding Purpose", "Personal Boundaries", "Phobias & Fears",
        "Spirituality & Faith Issues", "Domestic Violence Support", "Health & Wellness"
        ];
        return $this->renderDataTableView('admin.counselors.index', $config, compact('timezones','specialization'));
    }

    public function getData(Request $request)
    {
        $config = $this->getCounselorDataTableConfig();
        return $this->handleDataTableRequest($request, User::class, $config);
    }

    protected function getCounselorDataTableConfig(): array
    {
        return [
            'table_id' => 'counselors-datatable',
            'title' => 'All Counselors',
            'add_new_url' => '#',
            'add_new_text' => 'Add New Counsellor',
            'ajax_url' => route('admin.counselors.data'),
            'scope' => 'Counsellors',
            'columns' => [
                'id' => ['title' => '#'],
                'name' => ['title' => 'Name'],
                'email' => ['title' => 'Email'],
            ],
            'actions' => [
                'manage' => ['url' => fn($row) => route('admin.counselors.show', $row->id), 'class' => 'btn btn-primary mindway-btn-blue', 'text' => 'manage'],
                'availability' => ['url' => fn($row) => route('admin.counselors.availability', $row->id), 'class' => 'btn btn-primary mindway-btn-blue', 'text' => 'availability'],
                'profile' => ['url' => fn($row) => route('admin.counselors.edit', $row->id), 'class' => 'btn  btn-primary mindway-btn-blue', 'text' => 'profile'],
            ],
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:counselors,email',
            // ... add other validation rules
        ]);

        Counselor::create($validated);
        return redirect()->route('admin.counselors.index')->with('message', 'Counselor added successfully.');
    }

    /**
     * Display the specified resource. (The "Manage Sessions" page)
     */
    public function show(Counselor $counselor)
    {
        $timezone = $counselor->timezone ?? 'UTC';
        $upcomingBookings = Booking::with(['user', 'slot'])
            ->where('counselor_id', $counselor->id)
            ->where('status', 'confirmed')
            ->whereHas('slot', fn($q) => $q->where('start_time', '>', now()))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.counselors.show', [
            'counselor' => $counselor,
            'upcomingBookings' => $upcomingBookings,
            'timezone' => $timezone,
        ]);
    }

    /**
     * Show the form for editing the specified resource. (The "Profile Settings" page)
     */
    public function edit(Counselor $counselor)
    {
        $path = public_path('timezones.json');
        $timezones = json_decode(File::get($path), true);
        $specialization = json_decode($counselor->specialization, true) ?? [];

        return view('admin.counselors.edit', compact('counselor', 'timezones', 'specialization'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Counselor $counselor)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'notice_period' => 'nullable|integer',
            'gender' => 'required|in:Male,Female,Other',
            'tags' => 'nullable|string', // From Tagify
            'location' => 'nullable|string',
            'language' => 'nullable|array',
            'communication_methods' => 'nullable|array',
            'intake_link' => 'nullable|url',
        ]);

        $updateData = $validated;
        $updateData['specialization'] = json_encode(explode(',', $validated['tags']));
        $updateData['communication_method'] = json_encode($validated['communication_methods'] ?? []);
        
        // Handle file uploads if they exist
        if ($request->hasFile('logo')) {
            // ... logo upload logic ...
        }
        if ($request->hasFile('intro_video')) {
            // ... intro video upload logic ...
        }

        $counselor->update($updateData);
        return redirect()->route('admin.counselors.edit', $counselor->id)->with('message', 'Profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Counselor $counselor)
    {
        $counselor->delete(); // Assumes SoftDeletes
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Counselor moved to trash.']);
        }
        return redirect()->route('admin.counselors.index')->with('message', 'Counselor moved to trash.');
    }

    /**
     * Show the availability page for the counselor.
     */
    public function availability(Counselor $counselor)
    {
        $currentTimezone = $counselor->timezone;
        // ... logic from your old counsellorAvailability method ...
        return view('admin.counselors.availability', compact('counselor', 'currentTimezone'));
    }

    /**
     * Save the availability for the counselor.
     */
    public function saveAvailability(Request $request, Counselor $counselor)
    {
        // ... logic to save availability from the AJAX request ...
        return response()->json(['success' => true, 'message' => 'Availability saved.']);
    }
}
