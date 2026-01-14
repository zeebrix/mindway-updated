<?php

use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (!function_exists('loginUser')) {
    function loginUser()
    {
        return Auth::user();
    }
}
if (!function_exists('getTimezonesList')) {
    function getTimezonesList()
    {
        $path = public_path('assets' . DIRECTORY_SEPARATOR . 'timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);
        return $timezones;
    }
}
if (!function_exists('getProgramTrialInfo')) {
    function getProgramTrialInfo(User $user): array
    {
        $programDetail = $user->programDetail ?? null;

        if (!$programDetail || $programDetail->program_type != 2) {
            return [0, false];
        }

        $is_trial = true;
        $trial_end = $programDetail->trial_expire;

        if (empty($trial_end)) {
            return [0, $is_trial];
        }

        $today = Carbon::today();
        $targetDate = Carbon::parse($trial_end);

        $leftDays = $today->greaterThanOrEqualTo($targetDate)
            ? 0
            : $today->diffInDays($targetDate);

        return [$leftDays, $is_trial];
    }
}
if (!function_exists('getSessionsReasonRate')) {
    function getSessionsReasonRate(User $user, $department_id = null): array
    {
        $workRelatedReasons = [
            'Work Related',
            'Work Stress',
            'Workplace Conflicts',
            'Harassment/Bullying',
            'Performance Issues',
            'Organisational Change',
            'Burnout',
            'Other',
            'Personal Related',
        ];

        $sixMonthsAgo = Carbon::now()->subMonths(6);

        $query = \App\Models\Session::query()
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $sixMonthsAgo);

        if ($department_id) {
            $query->where('department_id', $department_id);
        }
        $sessions = $query->pluck('reason');
        $reasonCounts = array_fill_keys($workRelatedReasons, 0);
        foreach ($sessions as $reasonString) {
            if (!$reasonString) continue;

            $reasons = array_map('trim', explode(',', $reasonString));

            foreach ($reasons as $reason) {
                if (isset($reasonCounts[$reason])) {
                    $reasonCounts[$reason]++;
                }
            }
        }
        $personRelatedCount = $reasonCounts['Personal Related'];
        $otherReasonsCount = array_sum($reasonCounts) - $personRelatedCount;

        $totalCount = $personRelatedCount + $otherReasonsCount;

        return [
            'personRelatedCount' => $personRelatedCount,
            'workReasonsCount' => $otherReasonsCount,
            'workReasonsPercentage' => $totalCount ? ($otherReasonsCount / $totalCount) * 100 : 0,
            'personRelatedPercentage' => $totalCount ? ($personRelatedCount / $totalCount) * 100 : 0,
        ];
    }
}
if (!function_exists('calculateGrowth')) {
    function calculateGrowth(User $user, $department_id = null): array
    {
        $user_id = $user->id;
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Base query: users with customerDetail for this program
        $query = User::Customers()
            ->whereRelation('customerDetail', 'program_id', $user_id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by department_id in related customerDetail table
        if ($department_id) {
            $query->whereRelation('customerDetail', 'department_id', $department_id);
        }

        // Aggregate counts per month
        $monthlyCounts = $query
            ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn($row) => sprintf('%04d-%02d', $row->year, $row->month));

        // Prepare result arrays
        $growthData = [];
        $labels = [];

        for ($i = 0; $i < 12; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $key = $monthDate->format('Y-m');

            $count = $monthlyCounts[$key]->count ?? 0;

            // Cumulative
            if ($i > 0) {
                $count += $growthData[$i - 1];
            }

            $growthData[] = $count;
            $labels[] = $monthDate->format('M');
        }

        return [$growthData, $labels];
    }
}
if (!function_exists('calculateSessionGrowth')) {
    function calculateSessionGrowth(User $user, $department_id = null): array
    {
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Base query
        $query = Session::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Department filter if provided
        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        // Aggregate counts per month
        $monthlyCounts = $query
            ->selectRaw("YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count")
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->keyBy(fn($row) => sprintf('%04d-%02d', $row->year, $row->month));

        $growthData = [];
        $labels = [];

        // Generate 7 months labels and cumulative counts
        for ($i = 0; $i < 7; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            $key = $monthDate->format('Y-m');

            $count = $monthlyCounts[$key]->count ?? 0;

            // Cumulative sum
            if ($i > 0) {
                $count += $growthData[$i - 1];
            }

            $growthData[] = $count;
            $labels[] = $monthDate->format('M y');
        }

        return [$growthData, $labels];
    }
}

if (!function_exists('sessionReasons')) {
    function sessionReasons(User $user, $department_id = null): array
    {
        $workRelatedReasons = [
            'Work Related',
            'Work Stress',
            'Workplace Conflicts',
            'Harassment/Bullying',
            'Performance Issues',
            'Organisational Change',
            'Burnout',
            'Other',
            'Personal Related',
        ];

        $sixMonthsAgo = Carbon::now()->subMonths(6);

        // Base query
        $query = Session::where('user_id', $user->id)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select('reason');

        if ($department_id) {
            $query->where('department_id', $department_id);
        }

        // Fetch only reason column
        $sessions = $query->pluck('reason');

        // Flatten all reasons into a single array
        $allReasons = $sessions
            ->filter() // remove nulls
            ->flatMap(function ($reasonString) {
                return array_map('trim', explode(',', $reasonString));
            })
            ->toArray();

        // Count only valid reasons
        $reasonCounts = array_fill_keys($workRelatedReasons, 0);
        foreach ($allReasons as $reason) {
            if (in_array($reason, $workRelatedReasons)) {
                $reasonCounts[$reason]++;
            }
        }

        // Sort descending
        arsort($reasonCounts);

        return [array_keys($reasonCounts), array_values($reasonCounts)];
    }
}
if (!function_exists('getTimezones')) {
    function getTimezones()
    {
        $path = public_path('timezones.json');
        $json = File::get($path);
        $timezones = json_decode($json, true);
        return $timezones;
    }
}


function sendDynamicEmailFromTemplate($recipient, $subject, $template, $data)
{
    $data['subject'] = $subject; // Add subject to data
    try {
        Mail::to($recipient)->send(new SendEmail($template, $data));
        Log::info('Email sent successfully', [
            'recipient' => $recipient ?? 'N/A',
            'subject' => $subject ?? 'N/A',
            'template' => isset($template) ? json_encode($template) : 'N/A',
            'email_data' => isset($data) ? json_encode($data) : 'N/A',
        ]);
        return ['success' => true, 'message' => 'Email sent successfully.'];
    } catch (\Exception $e) {
        Log::error('Email sending failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to send email.'];
    }
}
