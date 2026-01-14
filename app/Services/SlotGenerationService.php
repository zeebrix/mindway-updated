<?php

namespace App\Services;

use App\Models\DeletedSlotLog;
use App\Models\Slot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SlotGenerationService
{
    private const SLOT_DURATION = 50;
    private GoogleProvider $googleProvider;
    private OutlookService $outlookService;

    public function __construct(GoogleProvider $googleProvider, OutlookService $outlookService)
    {
        $this->googleProvider = $googleProvider;
        $this->outlookService = $outlookService;
    }
    public function generateSlotsForCounselor(User $counselor, $month = null, $year = null)
    {
        $year = $year ? $year :  now()->year;
        if ($month) {
            $startDate = now()->setTimezone('UTC')->setDay(1)->setYear($year)->setMonth($month)->startOfMonth();

            $endDate = $startDate->copy()->endOfMonth();
            $existingSlots = $counselor->slots()
                ->whereBetween('date', [
                    $startDate->toDateString(),
                    $endDate->toDateString()
                ])
                ->where('is_booked', false)
                ->whereNull('customer_id')
                ->exists();
            if ($existingSlots) {
                return;
            }
        } else {
            $month = now()->month;
            $startDate = now()->setTimezone('UTC')->setDay(1)->setYear($year)->setMonth($month)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
        }

        // Delete future slots that aren't booked
        $counselor->slots()
            ->whereBetween('date', [
                $startDate->toDateString(),
                $endDate->toDateString()
            ])
            ->where('is_booked', false)
            ->whereNull('customer_id')
            ->delete();
        $timezone = $counselor->timezone ?? 'UTC';
        $startDate = $month ? now()->setTimezone($timezone)->setDay(1)->setYear($year)->setMonth($month)->startOfMonth() : now()->setTimezone($timezone)->setYear($year)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        while ($startDate <= $endDate) {
            $dayOfWeek = ($startDate->dayOfWeek + 6) % 7;
            // Get availability for this day
            $availability = $counselor->availabilities()
                ->where('day', $dayOfWeek)
                ->get();

            foreach ($availability as $schedule) {
                $this->generateSlotsForDay(
                    $counselor,
                    $startDate,
                    $schedule->start_time,
                    $schedule->end_time,
                    $timezone
                );
            }

            $startDate->addDay();
        }
        $this->removeConflictingSlots($counselor, $month, $year);
        return;
    }

    private function generateSlotsForDay(
        User $counselor,
        Carbon $date,
        $startTime,
        $endTime,
        string $timezone
    ) {
        $startTime = $startTime->setTimezone($timezone);
        $endTime = $endTime->setTimezone($timezone);
        $start = Carbon::parse($startTime, $timezone)->setDateFrom($date);
        $end = Carbon::parse($endTime, $timezone)->setDateFrom($date);
        if ($start->minute > 0 || $start->second > 0) {
            $start = $start->addHour()->minute(0)->second(0);
        }
        while ($start->copy()->addMinutes(self::SLOT_DURATION) <= $end) {
            $slotStart = $start->copy()->setTimezone('UTC');
            $slotEnd = $start->copy()->addMinutes(self::SLOT_DURATION)->setTimezone('UTC');
            $slot = Slot::where('counselor_id', $counselor->id)->where('start_time', $slotStart)->where('end_time', $slotEnd)->first();
            if (!$slot) {
                Slot::create([
                    'counselor_id' => $counselor->id,
                    'date' => $slotStart->toDateString(),
                    'start_time' => $slotStart,
                    'end_time' => $slotEnd,
                    'is_booked' => false,
                ]);
            }

            $start->addHour();
        }
    }
    public function removeConflictingSlots(User $counselor, ?string $month = null, $year = null)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        if ($month) {
            $month = (int) $month;

            // If the selected month has already passed this year → next year
            if ($month < $currentMonth) {
                $year = $currentYear + 1;
            } else {
                $year = $currentYear;
            }
        } else {
            $year = $currentYear;
        }
        try {
            // Ensure the counselor has a Google token
            if (!$counselor->googleToken || !$counselor->googleToken->access_token) {
                Log::warning("Google Token missing for counselor ID: {$counselor->id}");
                return;
            }

            $timezone = $counselor->timezone ?? 'UTC';

            if ($month) {

                $startOfMonth = Carbon::now($timezone)->setDay(1)->setYear($year)->setMonth($month)->startOfMonth();
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
            } else {
                // If no month is provided, check the entire year (Jan 1 - Dec 31)
                $startOfMonth = Carbon::now($timezone)->startOfYear();
                $endOfMonth = Carbon::now($timezone)->endOfYear();
            }

            // Fetch all events for the given range
            try {
                $events = $this->googleProvider->getAllEvents(
                    $counselor->googleToken->access_token,
                    $startOfMonth->toRfc3339String(),
                    $endOfMonth->toRfc3339String()
                );
            } catch (\Throwable $th) {
                $events = [];
            }

            if (empty($events)) {
                Log::info("No events found for counselor ID: {$counselor->id} in range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}");
                // return;
            }
            $outlookBusy = [];
            if ($counselor->outlookToken) {

                try {
                    $outlookBusy = $this->outlookService->getBusyTimes(
                        $counselor,
                        $startOfMonth->copy()->setTimezone('UTC')->toIso8601String(),
                        $endOfMonth->copy()->setTimezone('UTC')->toIso8601String()
                    );

                    Log::info("Fetched Outlook busy times for counselor ID {$counselor->id}");
                } catch (\Exception $ex) {
                    // dd($ex->getMessage());
                    Log::error("Failed to fetch Outlook busy times for counselor {$counselor->id}: {$ex->getMessage()}");
                }
            }
            // Convert event times to UTC
            foreach ($events as $key => $event) {
                try {
                    $startTz = !empty($event['start_timezone']) ? $event['start_timezone'] : $timezone;
                    $endTz = !empty($event['end_timezone']) ? $event['end_timezone'] : $timezone;

                    $events[$key]['start_time'] = Carbon::parse($event['start_time'], $startTz)->setTimezone('UTC');
                    $events[$key]['end_time'] = Carbon::parse($event['end_time'], $endTz)->setTimezone('UTC');
                } catch (\Exception $e) {
                    Log::error("Error parsing event times for event ID: {$event['event_id']}. Error: {$e->getMessage()}");
                    continue;
                }
            }
            $allBusy = [];
            foreach ($events as $ev) {
                if ($ev['summary'] === "50min Mindway EAP Session" || $ev['summary'] === "Standard Consultation") {
                    continue;
                }

                $allBusy[] = [
                    'start' => $ev['start_time'],
                    'end'   => $ev['end_time'],
                    'source' => 'google',
                    'event_id' => $ev['event_id']
                ];
            }
            foreach ($outlookBusy as $busy) {
                $allBusy[] = [
                    'start' => Carbon::parse($busy['start'])->setTimezone('UTC'),
                    'end'   => Carbon::parse($busy['end'])->setTimezone('UTC'),
                    'source' => 'outlook',
                    'event_id' => null
                ];
            }
            // Fetch all slots for the counselor in the given range
            $slots = Slot::where('counselor_id', $counselor->id)->where('is_booked', false)
                ->whereBetween('start_time', [
                    $startOfMonth->copy()->setTimezone('UTC'),
                    $endOfMonth->copy()->setTimezone('UTC')
                ])
                ->get();
            // Remove slots that overlap with events
            foreach ($slots as $slot) {
                foreach ($allBusy  as $busy) {
                    if ($slot->start_time < $busy['end'] && $slot->end_time > $busy['start']) {
                        DeletedSlotLog::create([
                            'counselor_id' => $slot->counselor_id,
                            'date'         => $slot->date,
                            'start_time'   => $slot->start_time,
                            'end_time'     => $slot->end_time,
                            'google_event_id' => $busy['source'] === 'google' ? $busy['event_id'] : 'null',
                            'source'       => $busy['source'],
                        ]);

                        Log::info("Deleting slot {$slot->id} — conflict with {$busy['source']}");

                        $slot->delete();
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            try {
                $json = json_decode($e->getMessage(), true);

                $status = $json['error']['status'] ?? 'unknown';
                if ($status == 'UNAUTHENTICATED') {
                    $counselor->update([
                        'google_id' => null,
                        'google_name' => null,
                        'google_email' => null,
                        'google_picture' => null,
                    ]);
                    $cacheKey = "calendar_reconnect_email_sent_{$counselor->id}";
                    if (!Cache::has($cacheKey)) {
                        Log::error("Exception Recorded Before Email");
                        $recipient = $counselor->email;
                        $subject = 'Urgent: Connect Calendar';
                        $template = 'emails.reconnect-calendar';
                        $data = [
                            'full_name' => $counselor->name,
                        ];
                        sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
                        sendDynamicEmailFromTemplate('farahanjdfunnel@gmail.com', $subject, $template, $data);
                        Cache::put($cacheKey, true, now()->addHours(24));
                        Log::error("Exception Recorded.");
                    } else {
                        Log::info("Reconnect calendar email already sent within 24 hours to counselor ID: {$counselor->id}");
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
            Log::error("Error in removeConflictingSlots for counselor ID: {$counselor->id}, range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}. Exception: " . $e->getMessage());
        }
    }
    public function restoreAvailableSlots2(Counselor $counselor, ?string $month = null)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        if ($month) {
            $month = (int) $month;

            // If the selected month has already passed this year → next year
            if ($month < $currentMonth) {
                $year = $currentYear + 1;
            } else {
                $year = $currentYear;
            }
        } else {
            $year = $currentYear;
        }
        try {
            // Ensure the counselor has a Google token
            if ((!$counselor->googleToken || !$counselor->googleToken->access_token) && !$counselor->outlookToken) {
                Log::warning("Google Token missing for counselor ID: {$counselor->id}");
                return;
            }

            $timezone = $counselor->timezone ?? 'UTC';

            if ($month) {
                $startOfMonth = Carbon::now($timezone)->setDay(1)->setYear($year)->setMonth($month)->startOfMonth();
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
            } else {
                $startOfMonth = Carbon::now($timezone)->startOfYear();
                $endOfMonth = Carbon::now($timezone)->endOfYear();
            }

            // Fetch current events
            $events = $this->googleProvider->getAllEvents(
                $counselor->googleToken->access_token,
                $startOfMonth->toRfc3339String(),
                $endOfMonth->toRfc3339String()
            );

            // Convert event times to UTC
            foreach ($events as $key => $event) {
                try {
                    $startTz = !empty($event['start_timezone']) ? $event['start_timezone'] : $timezone;
                    $endTz = !empty($event['end_timezone']) ? $event['end_timezone'] : $timezone;

                    $events[$key]['start_time'] = Carbon::parse($event['start_time'], $startTz)->setTimezone('UTC');
                    $events[$key]['end_time'] = Carbon::parse($event['end_time'], $endTz)->setTimezone('UTC');
                } catch (\Exception $e) {
                    Log::error("Error parsing event times for event ID: {$event['event_id']}. Error: {$e->getMessage()}");
                    continue;
                }
            }
            $currentEventIds = collect($events)->pluck('event_id')->toArray();
            // Fetch previously deleted slots from logs for this counselor within the date range
            $logs = DeletedSlotLog::where('counselor_id', $counselor->id)
                ->whereBetween('start_time', [$startOfMonth->setTimezone('UTC'), $endOfMonth->setTimezone('UTC')])
                ->get();

            foreach ($logs as $log) {
                if (in_array($log->google_event_id, $currentEventIds)) {
                    // Event still exists, skip restoring
                    continue;
                }
                // Check if slot already exists (might have been manually created or restored)
                $existingSlot = Slot::where('counselor_id', $log->counselor_id)
                    ->where('start_time', $log->start_time)
                    ->where('end_time', $log->end_time)
                    ->where('date', $log->date)
                    ->first();

                if (!$existingSlot) {
                    Slot::create([
                        'counselor_id' => $log->counselor_id,
                        'date'         => $log->date,
                        'start_time'   => $log->start_time,
                        'end_time'     => $log->end_time,
                        'is_booked'    => false,
                    ]);
                }
                $log->delete();
            }
        } catch (\Exception $e) {
            Log::error("Error in restoreAvailableSlots for counselor ID: {$counselor->id}, range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}. Exception: " . $e->getMessage());
        }
    }
    public function restoreAvailableSlots(Counselor $counselor, ?string $month = null)
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;
        if ($month) {
            $month = (int) $month;

            // If the selected month has already passed this year → next year
            if ($month < $currentMonth) {
                $year = $currentYear + 1;
            } else {
                $year = $currentYear;
            }
        } else {
            $year = $currentYear;
        }
        try {
            // Ensure the counselor has Google or Outlook token
            if ((!$counselor->googleToken || !$counselor->googleToken->access_token) && !$counselor->outlookToken) {
                Log::warning("No calendar tokens available for counselor ID: {$counselor->id}");
                return;
            }

            $timezone = $counselor->timezone ?? 'UTC';

            if ($month) {
                $startOfMonth = Carbon::now($timezone)->setDay(1)->setYear($year)->setMonth($month)->startOfMonth();
                $endOfMonth = $startOfMonth->copy()->endOfMonth();
            } else {
                $startOfMonth = Carbon::now($timezone)->startOfYear();
                $endOfMonth = Carbon::now($timezone)->endOfYear();
            }

            $allBusy = [];

            // -----------------------------
            // 1. Fetch Google events
            // -----------------------------
            if ($counselor->googleToken && $counselor->googleToken->access_token) {
                try {
                    $events = $this->googleProvider->getAllEvents(
                        $counselor->googleToken->access_token,
                        $startOfMonth->toRfc3339String(),
                        $endOfMonth->toRfc3339String()
                    );
                } catch (\Throwable $th) {
                    $events = [];
                }

                foreach ($events as $key => $event) {
                    try {
                        $startTz = $event['start_timezone'] ?? $timezone;
                        $endTz = $event['end_timezone'] ?? $timezone;

                        $events[$key]['start_time'] = Carbon::parse($event['start_time'], $startTz)->setTimezone('UTC');
                        $events[$key]['end_time'] = Carbon::parse($event['end_time'], $endTz)->setTimezone('UTC');
                    } catch (\Exception $e) {
                        Log::error("Error parsing Google event times for event ID: {$event['event_id']}. Error: {$e->getMessage()}");
                        continue;
                    }
                }

                foreach ($events as $ev) {
                    $allBusy[] = [
                        'start' => $ev['start_time'],
                        'end' => $ev['end_time'],
                        'source' => 'google',
                        'event_id' => $ev['event_id'],
                    ];
                }
            }

            // -----------------------------
            // 2. Fetch Outlook events
            // -----------------------------
            if ($counselor->outlookToken) {
                try {
                    $outlookBusy = $this->outlookService->getBusyTimes(
                        $counselor,
                        $startOfMonth->copy()->setTimezone('UTC')->toIso8601String(),
                        $endOfMonth->copy()->setTimezone('UTC')->toIso8601String()
                    );
                    foreach ($outlookBusy as $busy) {
                        $allBusy[] = [
                            'start' => Carbon::parse($busy['start'])->setTimezone('UTC'),
                            'end' => Carbon::parse($busy['end'])->setTimezone('UTC'),
                            'source' => 'outlook',
                            'event_id' => null,
                        ];
                    }
                } catch (\Exception $ex) {
                    // dd($ex->getMessage());
                    Log::error("Failed to fetch Outlook busy times for counselor {$counselor->id}: {$ex->getMessage()}");
                }
            }

            // -----------------------------
            // 3. Restore previously deleted slots
            // -----------------------------
            $logs = DeletedSlotLog::where('counselor_id', $counselor->id)
                ->whereBetween('start_time', [$startOfMonth->copy()->setTimezone('UTC'), $endOfMonth->copy()->setTimezone('UTC')])
                ->get();

            foreach ($logs as $log) {

                // Check if Google event still exists
                if ($log->google_event_id && in_array($log->google_event_id, collect($events ?? [])->pluck('event_id')->toArray())) {
                    continue; // event still exists, skip restoring
                }

                // Check for conflicts with current busy times (Google + Outlook)
                $conflict = false;
                foreach ($allBusy as $busy) {
                    if ($log->start_time < $busy['end'] && $log->end_time > $busy['start']) {
                        $conflict = true;
                        break;
                    }
                }
                if ($conflict) continue;

                // Check if slot already exists
                $existingSlot = Slot::where('counselor_id', $log->counselor_id)
                    ->where('start_time', $log->start_time)
                    ->where('end_time', $log->end_time)
                    ->where('date', $log->date)
                    ->first();

                if (!$existingSlot) {
                    Slot::create([
                        'counselor_id' => $log->counselor_id,
                        'date' => $log->date,
                        'start_time' => $log->start_time,
                        'end_time' => $log->end_time,
                        'is_booked' => false,
                    ]);
                }

                $log->delete();
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
            Log::error("Error in restoreAvailableSlots for counselor ID: {$counselor->id}, range: {$startOfMonth->format('Y-m-d')} - {$endOfMonth->format('Y-m-d')}. Exception: " . $e->getMessage());
        }
    }
    public function removeConflictingSlots1(Counselor $counselor, string $month = null)
    {
        try {
            // Ensure the counselor has a Google token
            if (!$counselor->googleToken || !$counselor->googleToken->access_token) {
                Log::warning("Google Token missing for counselor ID: {$counselor->id}");
                return;
            }

            $timezone = $counselor->timezone ?? 'UTC';
            $startOfMonth = Carbon::now($timezone)->setDay(1)->setMonth($month)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            // Fetch all events for the given month
            $events = $this->googleProvider->getAllEvents(
                $counselor->googleToken->access_token,
                $startOfMonth->toRfc3339String(),
                $endOfMonth->toRfc3339String()
            );

            if (empty($events)) {
                Log::info("No events found for counselor ID: {$counselor->id} in month: {$month}");
                return;
            }
            Log::info("All events for counselor ID {$counselor->id}: " . json_encode($events));

            // Convert event times to UTC
            foreach ($events as &$event) {
                try {
                    $event['start_time'] = Carbon::parse($event['start_time'], $event['start_timezone'] ?? $timezone)->setTimezone('UTC');
                    $event['end_time'] = Carbon::parse($event['end_time'], $event['end_timezone'] ?? $timezone)->setTimezone('UTC');
                } catch (\Exception $e) {
                    Log::error("Error parsing event times for event ID: {$event['event_id']}");
                    continue;
                }
            }

            // Fetch all slots for the counselor in the given month
            $slots = Slot::where('counselor_id', $counselor->id)->where('is_booked', false)
                ->whereBetween('start_time', [$startOfMonth->setTimezone('UTC'), $endOfMonth->setTimezone('UTC')])
                ->get();

            // Remove slots that overlap with events
            foreach ($slots as $slot) {
                foreach ($events as $event) {
                    if ($slot->start_time < $event['end_time'] && $slot->end_time > $event['start_time']) {
                        Log::info("Deleting slot ID: {$slot->id} as it conflicts with event ID: {$event['event_id']}");
                        $slot->delete();
                        break; // No need to check further, slot is already deleted
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error in removeConflictingSlots for counselor ID: {$counselor->id}, month: {$month}. Exception: " . $e->getMessage());
        }
    }
}
