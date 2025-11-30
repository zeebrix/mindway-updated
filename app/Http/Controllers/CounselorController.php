<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CounselorController extends Controller
{
    public function dashboard(): View
    {
        $counselor = loginUser();
        $customers = User::whereHas('customerDetail')->get();
        $upcomingBookings = Booking::where('counselor_id', $counselor->id)
            ->where('status', Booking::Confirmed)
            ->whereHas('slot', function ($query) {
                $query->where('start_time', '>', now()->subHours(24));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $timezone = $counselor?->timezone ?? 'UTC';
        return view('counselor.dashboard', [
            'upcomingBookings' => $upcomingBookings,
            'customers' => $customers,
            'counselor' => $counselor,
            'timezone'  => $timezone
        ]);
    }
    public function getAvailability()
    {
        $counselor = loginUser();
        $currentTimezone = $counselor?->timezone ?? 'UTC';
        $timezones = getTimezonesList();
        $availabilityData = [];

        foreach ($counselor->availabilities as $slot) {
            $start = Carbon::parse($slot->start_time)->setTimezone($currentTimezone)->format('H:i');
            $end   = Carbon::parse($slot->end_time)->setTimezone($currentTimezone)->format('H:i');

            $availabilityData[$slot->day] = [
                'available'   => $slot->available,
                'start_time'  => $start,
                'end_time'    => $end,
            ];
        }

        return view('counselor.availability', compact(
            'availabilityData',
            'currentTimezone',
            'timezones',
            'counselor'
        ));
    }
    public function fetchCounsellorAvailability(Request $request)
    {
        $availabilities = Availability::where('counselor_id', $request->counselorId)->get();

        $availability = [];
        $currentTimezone = (count($availabilities) > 0) ? $availabilities[0]?->counselor?->timezone : 'UTC';
        if (!empty($availabilities)) {
            foreach ($availabilities as $data) {
                $startTimeInCounselorTimezone = Carbon::parse($data->start_time)->setTimezone($currentTimezone);
                $endTimeInCounselorTimezone = Carbon::parse($data->end_time)->setTimezone($currentTimezone);
                $availability[] = [
                    'day_of_week' => $data->day,
                    'start_time' => $startTimeInCounselorTimezone->format('H:i'),
                    'end_time' => $endTimeInCounselorTimezone->format('H:i'),
                ];
            }
        }
        return response()->json([
            'timeZones' => $currentTimezone,
            'availability' => $availability,
        ]);
    }
    public function profile()
    {
        $user = loginUser();
        $timezones = getTimezones();
        $fields = [

            [
                'type' => 'radio',
                'label' => 'Gender',
                'name'  => 'gender',
                'options' => ['Male', 'Female', 'Other'],
                'col' => 6
            ],

            [
                'type' => 'number',
                'label' => 'Notice Period (hours)',
                'name'  => 'notice_period',
                'col' => 4
            ],

            [
                'type' => 'textarea',
                'label' => 'Description',
                'name'  => 'description',
                'col' => 12
            ],

            [
                'type' => 'select',
                'label' => 'Location',
                'name'  => 'location',
                'col'   => 12
            ],

            [
                'type' => 'select',
                'label' => 'Language',
                'name'  => 'language',
                'multiple' => true,
                'col' => 12
            ],

            [
                'type' => 'checkbox',
                'label' => 'Communication Methods',
                'name'  => 'communication_method',
                'options' => ['Phone Call', 'Video Call'],
                'col' => 8
            ],

            [
                'type' => 'file',
                'label' => 'Logo Upload',
                'name'  => 'logo',
                'col' => 4
            ],

            [
                'type' => 'file',
                'label' => 'Intro Video',
                'name'  => 'intro_video',
                'col' => 6
            ],
        ];

        return view('counselor.profile', get_defined_vars());
    }
    public function getSettings()
    {

        $user = loginUser();

        list($leftDays, $is_trial) = getProgramTrialInfo($user);

        $plan = $user->programPlan;
        $secret = null;
        $qrCodeUrl = null;
        if ($user->is_2fa_enabled) {
            $google2fa = new Google2FA();
            $secret = $user->google2fa_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
        }
        return view('program.settings.index', get_defined_vars());
    }
}
