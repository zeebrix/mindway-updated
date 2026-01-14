<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\User;
use App\Services\SlotGenerationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;

class CounselorController extends Controller
{
    public function dashboard(): View
    {
        $counselor = loginUser();
        $customers = User::whereHas('customerDetail')->get();
        $upcomingBookings = Booking::where('user_id', $counselor->id)
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
        $user = loginUser();
        $counselor = $user->counsellorDetail;
        $currentTimezone = $counselor?->timezone ?? 'UTC';
        $timezones = getTimezonesList();
        $availabilityData = [];

        foreach ($user->availabilities as $slot) {
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
            'counselor',
            'user'
        ));
    }
    public function fetchCounsellorAvailability(Request $request)
    {
        $availabilities = Availability::where('user_id', $request->counselorId)->get();
        $availability = [];
        $currentTimezone = $availabilities[0]?->user?->timezone ?? 'UTC';
        $currentTimezone = $currentTimezone ? $currentTimezone : 'UTC';
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
        $counselor = $user->counsellorDetail;
        $timezones = getTimezones();


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
    public function setAvailability(Request $request)
    {
        $validated = $request->validate([
            'availability' => 'present|array',
        ]);

        $availabilityData = $validated['availability'];
        $user_id = $request->counselorId;
        $counselor = User::findOrFail($user_id);

        DB::beginTransaction();
        try {
            // Delete existing availabilities
            $counselor->availabilities()->delete();

            foreach ($availabilityData as $dayAvailability) {
                if (!empty($dayAvailability['start_time']) && !empty($dayAvailability['end_time'])) {
                    $startTime = Carbon::parse($dayAvailability['start_time'], $counselor->timezone);
                    $endTime = Carbon::parse($dayAvailability['end_time'], $counselor->timezone);

                    $startTimeUtc = $startTime->setTimezone('UTC')->format('H:i:s');
                    $endTimeUtc = $endTime->setTimezone('UTC')->format('H:i:s');

                    $counselor->availabilities()->create([
                        'day' => $dayAvailability['day_of_week'],
                        'start_time' => $startTimeUtc,
                        'end_time' => $endTimeUtc,
                        'available' => true,
                    ]);
                }
            }

            // Delete unbooked slots and regenerate
            $counselor->slots()->where('is_booked', false)->delete();
            app(SlotGenerationService::class)->generateSlotsForCounselor($counselor);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Availability updated successfully.'
                ]);
            }

            return redirect()->route('counsellor.index')->with('success', 'Availability updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            Log::error('Error updating availability', ['error' => $e->getMessage()]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating availability. Please try again later.'
                ], 500);
            }

            return redirect()->route('counsellor.index')->with('error', 'An error occurred while updating availability. Please try again later.');
        }
    }
}
