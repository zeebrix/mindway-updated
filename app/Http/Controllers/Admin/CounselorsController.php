<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\Booking;
use App\Models\CounsellingSession;
use App\Models\CounsellorDetail;
use App\Models\User;
use App\Services\SlotGenerationService;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

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
            "Stress",
            "Burnout",
            "Anxiety",
            "Depression",
            "Grief & Loss",
            "Sleep Difficulties",
            "Conflict Resolution",
            "Family & Relationship Issues",
            "Leader/Manager Support",
            "Addiction",
            "Trauma & PTSD",
            "Work-Life Balance",
            "Personal Development",
            "Career Counselling",
            "Mindfulness",
            "Coping Strategies",
            "Life Transitions",
            "Anger Management",
            "Confidence Building",
            "Parenting Support",
            "Sexuality & Identity Issues",
            "Workplace Bullying & Harassment",
            "Communication Skills",
            "Motivation & Goal Setting",
            "Eating Disorders",
            "Body Image Issues",
            "Cognitive Behavioural Therapy (CBT)",
            "Emotional Regulation",
            "Finding Purpose",
            "Personal Boundaries",
            "Phobias & Fears",
            "Spirituality & Faith Issues",
            "Domestic Violence Support",
            "Health & Wellness"
        ];
        return $this->renderDataTableView('admin.counselors.index', $config, compact('timezones', 'specialization'));
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
            'open_in_modal' => true,
            'target_modal_id' => 'addCounsellorModal',
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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) {
                    $query->where('user_type', 'counselor');
                }),
            ],
            'description' => 'required|string|max:1000',
            'gender' => ['required', Rule::in(['Male', 'Female', 'Other'])],
            'communication_method' => 'required|array|min:1',
            'communication_method.*' => ['string', Rule::in(['Video Call', 'Phone Call'])],
            'location' => 'required|string|max:255',
            'language' => 'required|array|min:1',
            'language.*' => 'string|max:255',
            'tags' => 'required|array|max:255',
            'timezone' => 'required|string|timezone',
        ]);
        $specializations = [];

        if ($request->filled('tags')) {

            $tags = $request->tags;

            if (is_string($tags)) {
                $tags = json_decode($tags, true) ?? [];
            }

            if (is_array($tags)) {
                $specializations = collect($tags)
                    ->flatMap(function ($item) {

                        if (is_string($item)) {
                            return array_map('trim', explode(',', $item));
                        }

                        if (is_array($item) && isset($item['value'])) {
                            return [$item['value']];
                        }

                        return [];
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'user_type' => 'counsellor',
            'status' => 'active',
        ]);

        CounsellorDetail::create([
            'user_id' => $user->id,
            'gender' => $validated['gender'],
            'description' => $validated['description'],
            'timezone' => $validated['timezone'],
            'location' => $validated['location'],
            'language' => json_encode($validated['language']),
            'communication_method' => json_encode($validated['communication_method']),
            'specialization' => json_encode($specializations),
        ]);
        $recipient =  $validated['email'];
        $subject = 'Welcome to Mindway EAP – Set Up Your Profile';
        $template = 'emails.counsellor-setup-profile';
        $token = encrypt($user->id);
        $resetLink = url("/set-password-view/?token={$token}&email={$request->email}&type=counsellorSetPassword&name{$request->name}");
        $data = [
            'full_name' => $validated['name'],
            'resetLink' => $resetLink,
        ];
        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
        return redirect()->route('admin.counselors.index')->with('message', 'Counselor added successfully.');
    }

    /**
     * Display the specified resource. (The "Manage Sessions" page)
     */
    public function show(Request $request, $id)
    {
        $user = User::findorfail($id);
        $counselor = $user->counsellorDetail;
        $timezone = $counselor->timezone ?? 'UTC';
        $upcomingBookings = Booking::with(['user', 'slot'])
            ->where('counselor_id', $counselor?->id)
            ->where('status', Booking::Confirmed)
            ->whereHas('slot', fn($q) => $q->where('start_time', '>', now()))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.counselors.show', [
            'user' => $user,
            'upcomingBookings' => $upcomingBookings,
            'timezone' => $timezone,
        ]);
    }

    /**
     * Show the form for editing the specified resource. (The "Profile Settings" page)
     */
    public function edit(Request $request, $id)
    {
        $path = public_path('timezones.json');
        $timezones = json_decode(File::get($path), true);
        $user = User::findorfail($id);
        $counselor = $user->counsellorDetail;
        $specialization = json_decode($counselor?->specialization, true) ?? [];

        return view('admin.counselors.edit', compact('counselor', 'user', 'timezones', 'specialization'));
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
    public function availability(User $user)
    {
        $currentTimezone = $user?->counsellorDetail?->timezone ?? 'UTC';
        return view('admin.counselors.availability', compact('user', 'currentTimezone'));
    }

    /**
     * Save the availability for the counselor.
     */
    public function saveAvailability(Request $request, Counselor $counselor)
    {
        // ... logic to save availability from the AJAX request ...
        return response()->json(['success' => true, 'message' => 'Availability saved.']);
    }
    public function getSessionData(Request $request)
    {
        $sessions = CounsellingSession::where('counselor_id', $request->counsellor_id)->with('customer');
        return DataTables::of($sessions)
            ->addColumn('name_email', function ($session) {
                if (!$session->customer) {
                    return '-';
                }

                return $session->customer->name . '<br><small>' . $session->customer->email . '</small>';
            })
            ->addColumn('company_name', function ($session) {
                if (!$session->program) {
                    return '-';
                }

                return $session?->program?->company_name;
            })
            ->addColumn('company_email', function ($session) {
                if (!$session->program) {
                    return '-';
                }

                return $session?->program?->user->email;
            })
            ->addColumn('counsellor_name', function ($session) {
                if (!$session->counsellor) {
                    return '-';
                }

                return $session?->counsellor?->name;
            })

            ->addColumn('level', function ($session) {
                return $session->customer?->customerDetail->level ?? '-';
            })

            ->addColumn('max_session', function ($session) {
                return $session->customer?->customerDetail->max_sessions ?? '-';
            })



            ->rawColumns(['name_email', 'counsellor_name', 'company_email', 'company_name'])
            ->make(true);
    }
    public function cancelSession(Request $request)
    {
        $booking = Booking::findOrFail($request['booking_id']);
        if ($booking->status == 'cancelled') {
            return redirect()->back()->with('error', 'Booking already cancelled successfully');
        }
        $user_id = $request['customer_id'];
        if ((int)$booking->user_id !== (int)$user_id) {
            return redirect()->back()->with('error', 'Not allowed to cancel the session');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->slot->update(['is_booked' => false]);
        return redirect()->back()->with('success', 'Booking cancelled successfully');
    }
    public function saveProfileField(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'counselorId' => 'required|integer|exists:users,id',
            'key'         => 'required|string',
            'value'       => 'nullable|string',
            'file'        => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:51200', // 50MB max
        ]);

        $counselorUser = User::find($validated['counselorId']);
        $counselor = $counselorUser->counsellorDetail;

        DB::beginTransaction();
        try {
            // Handle file uploads
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                if ($validated['key'] === 'introduction_video') {
                    $path = $file->store('Intro', 'public');
                    $counselor->introduction_video = $path;
                } elseif ($validated['key'] === 'avatar') {
                    $path = $file->store('Avatar', 'public');
                    $counselor->avatar = $path;
                }
            } else {
                // Normal field update
                $counselor->{$validated['key']} = $validated['value'] ?? null;
            }

            $counselor->save();
            DB::commit();

            // Regenerate slots if timezone changed
            if ($validated['key'] === 'timezone') {
                app(SlotGenerationService::class)->generateSlotsForCounselor($counselorUser);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Field saved successfully!',
                'path'    => $path ?? null, // return file path if uploaded
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function saveProfile(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'intake_link' => 'nullable|url',
            'notice_period' => 'nullable|integer|min:0',
            'location' => 'required|string',
            'language' => 'required|array',
            'language.*' => 'string',
            'tags' => 'nullable|string',
            'communication_methods' => 'nullable|array',
            'communication_methods.*' => 'string',
        ]);

        $counsellorId = $request->counselor_id;
        $counselorUser = User::where('id', $counsellorId)->first();
        $counselorDetail = $counselorUser->counsellorDetail;
        // Update core fields
        $counselorDetail->description = $request->input('description', $counselorDetail->description);
        $counselorDetail->gender = $request->input('gender', $counselorDetail->gender);
        $counselorDetail->intake_link = $request->input('intake_link', $counselorDetail->intake_link);
        $counselorDetail->notice_period = $request->input('notice_period', $counselorDetail->notice_period);
        $counselorDetail->location = $request->input('location', $counselorDetail->location);

        // Specialization handling
        $specializations = [];

        // If tags exist
        if ($request->filled('tags')) {

            // If tags come as JSON string
            if (is_string($request->tags)) {
                $decoded = json_decode($request->tags, true);

                if (is_array($decoded)) {
                    $specializations = collect($decoded)
                        ->pluck('value')
                        ->filter()
                        ->values()
                        ->toArray();
                }

                // If tags come as array
            } elseif (is_array($request->tags)) {
                $specializations = collect($request->tags)
                    ->pluck('value')
                    ->filter()
                    ->values()
                    ->toArray();
            }
        }

        // Save clean JSON
        $counselorDetail->specialization = json_encode($specializations);
        // Language handling
        $counselorDetail->language = json_encode($request->input('language', []));

        // Communication methods
        $counselorDetail->communication_method = json_encode($request->input('communication_methods', []));

        // Save counselor
        $counselorDetail->save();

        // Handle notice period update: regenerate slots
        if ($request->filled('notice_period')) {
            $counselorUser->slots()->where('is_booked', false)->delete();

            // Generate slots for current and next month
            $currentMonth = now()->month;
            $nextMonth = now()->addMonth()->month;
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselorUser, $currentMonth);
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselorUser, $nextMonth);
        }

        return back()->with(['message' => "Profile information saved successfully."]);
    }
}
