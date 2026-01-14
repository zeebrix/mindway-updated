<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OutlookService
{
    private function http()
    {
        return new Client([
            'timeout'  => 20,
        ]);
    }

    private function graph(string $url)
    {
        return "https://graph.microsoft.com/v1.0/" . ltrim($url, '/');
    }

    private function oauth(string $url)
    {
        return "https://login.microsoftonline.com/" . env('OUTLOOK_TENANT_ID') . "/" . ltrim($url, '/');
    }

    // -----------------------------------------------------------
    // 1) Exchange Authorization Code for Access/Refresh Tokens
    // -----------------------------------------------------------
    public function getTokens($code)
    {
        $client = $this->http();

        $response = $client->post($this->oauth('oauth2/v2.0/token'), [
            'form_params' => [
                'client_id'     => env('OUTLOOK_CLIENT_ID'),
                'client_secret' => env('OUTLOOK_CLIENT_SECRET'),
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => env('OUTLOOK_REDIRECT_URI'),
            ]
        ]);
        return json_decode($response->getBody(), true);
    }

    // -----------------------------------------------------------
    // 2) Refresh Expired Access Token
    // -----------------------------------------------------------
    public function refreshToken($refreshToken)
    {
        $client = $this->http();

        $response = $client->post($this->oauth('oauth2/v2.0/token'), [
            'form_params' => [
                'client_id'     => env('OUTLOOK_CLIENT_ID'),
                'client_secret' => env('OUTLOOK_CLIENT_SECRET'),
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken,
                'redirect_uri'  => env('OUTLOOK_REDIRECT_URI'),
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    // -----------------------------------------------------------
    // 3) Resolve Working Access Token
    // -----------------------------------------------------------
    public function getActiveAccessToken($counselor)
    {
        $outlook = $counselor->outlookToken;
        $accessToken  = decrypt($outlook->access_token);
        $refreshToken = decrypt($outlook->refresh_token);

        if (now()->greaterThan($outlook->expires_in)) {
            $newTokens = $this->refreshToken($refreshToken);

            $outlook->update([
                'access_token'  => encrypt($newTokens['access_token']),
                'refresh_token' => encrypt($newTokens['refresh_token']),
                'expires_in'    => now()->addSeconds($newTokens['expires_in']),
            ]);

            return $newTokens['access_token'];
        }

        return $accessToken;
    }

    // -----------------------------------------------------------
    // 4) Get Current Outlook User Info
    // -----------------------------------------------------------
    public function getUserInfo(string $accessToken)
    {
        $client = $this->http();

        try {
            $response = $client->get($this->graph("me"), [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Accept'        => 'application/json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return ['error' => json_decode($e->getResponse()->getBody(), true)];
        }
    }

    // -----------------------------------------------------------
    // 5) Check Busy Times
    // -----------------------------------------------------------
    public function getBusyTimes($counselor, $startUtc, $endUtc)
    {
        $client = $this->http();
        $accessToken = $this->getActiveAccessToken($counselor);

        /**
         * 1. Get mailbox timezone
         */
        $mailboxResponse = $client->get($this->graph("me/mailboxSettings"), [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Accept'        => 'application/json',
            ]
        ]);

        $mailboxData = json_decode($mailboxResponse->getBody(), true);
        $windowsTz = $mailboxData['timeZone'] ?? 'UTC';
        $mailboxTimeZone = $this->windowsToIana($windowsTz);

        /**
         * 2. Fetch ALL calendars
         */
        $calList = $client->get($this->graph("me/calendars"), [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Accept'        => 'application/json',
            ]
        ]);

        $calendars = json_decode($calList->getBody(), true)['value'] ?? [];
        $busy = [];

        /**
         * 3. Loop through each calendar
         */
        foreach ($calendars as $calendar) {

            $calendarId = $calendar['id'];

            // Fetch events for this calendar
            $eventsResponse = $client->get($this->graph("me/calendars/{$calendarId}/calendarView"), [
                'headers' => [
                    'Authorization' => "Bearer {$accessToken}",
                    'Accept'        => 'application/json',
                ],
                'query' => [
                    'startDateTime' => $startUtc,
                    'endDateTime'   => $endUtc,
                ]
            ]);

            $eventsData = json_decode($eventsResponse->getBody(), true);

            foreach ($eventsData['value'] as $event) {

                // Convert Outlook Windows timezone to IANA
                $eventTz = $this->windowsToIana($event['start']['timeZone']);

                $startUtcObj = Carbon::parse($event['start']['dateTime'], $eventTz)
                    ->setTimezone('UTC');

                $endUtcObj = Carbon::parse($event['end']['dateTime'], $eventTz)
                    ->setTimezone('UTC');

                $startLocal = $startUtcObj->clone()->setTimezone($mailboxTimeZone);
                $endLocal   = $endUtcObj->clone()->setTimezone($mailboxTimeZone);

                $busy[] = [
                    'calendar_id' => $calendarId,
                    'calendar_name' => $calendar['name'] ?? 'Unknown',

                    'start'   => $startUtcObj->toIso8601String(),
                    'end'     => $endUtcObj->toIso8601String(),

                    'start_local' => $startLocal->format('Y-m-d H:i:s'),
                    'end_local'   => $endLocal->format('Y-m-d H:i:s'),

                    'local_tz' => $mailboxTimeZone,
                ];
            }
        }
        return $busy;
    }




    private function windowsToIana($windowsTz)
    {
        $map = [
            "Pacific Standard Time" => "America/Los_Angeles",
            "Eastern Standard Time" => "America/New_York",
            "Central Standard Time" => "America/Chicago",
            "Mountain Standard Time" => "America/Denver",
            "GMT Standard Time" => "Europe/London",
            "Pakistan Standard Time" => "Asia/Karachi",
            "India Standard Time" => "Asia/Kolkata",
            "Arabian Standard Time" => "Asia/Dubai",
            "Central Europe Standard Time" => "Europe/Berlin",
            "W. Europe Standard Time" => "Europe/Berlin",
            "China Standard Time" => "Asia/Shanghai",
            "SE Asia Standard Time" => "Asia/Bangkok",
            "Tokyo Standard Time" => "Asia/Tokyo",
            // add more as needed
        ];

        return $map[$windowsTz] ?? 'UTC';
    }

    public function isBusy($counselor, Carbon $start, Carbon $end)
    {
        if (!$counselor->outlook_access_token) {
            return false;
        }

        $accessToken = $this->getActiveAccessToken($counselor);

        $busy = $this->getBusyTimes(
            $accessToken,
            $start->toIso8601String(),
            $end->toIso8601String()
        );

        return count($busy) > 0;
    }

    // -----------------------------------------------------------
    // 6) Create Placeholder Event
    // -----------------------------------------------------------
    public function createPlaceholderEvent($counselor, Carbon $start, Carbon $end)
    {
        $token = $this->getActiveAccessToken($counselor);

        $client = $this->http();

        $event = [
            "subject" => "Mindway Booking (Busy)",
            "showAs"  => "busy",
            "start" => [
                "dateTime" => $start->toIso8601String(),
                "timeZone" => "UTC"
            ],
            "end" => [
                "dateTime" => $end->toIso8601String(),
                "timeZone" => "UTC"
            ],
            "body" => [
                "contentType" => "text",
                "content"     => ""
            ]
        ];

        $response = $client->post($this->graph("me/events"), [
            'headers' => [
                'Authorization' => "Bearer {$token}",
                'Content-Type'  => 'application/json',
            ],
            'json' => $event
        ]);
        return json_decode($response->getBody(), true);
    }

    // -----------------------------------------------------------
    // 7) Update Placeholder Event
    // -----------------------------------------------------------
    public function updatePlaceholderEvent($counselor, $eventId, $startUtc, $endUtc)
    {
        if (!$eventId) return null;

        $token = $this->getActiveAccessToken($counselor);

        $client = $this->http();

        $body = [
            "subject" => "Mindway Booking (Busy)",
            "start" => [
                "dateTime" => $startUtc->toIso8601String(),
                "timeZone" => "UTC"
            ],
            "end" => [
                "dateTime" => $endUtc->toIso8601String(),
                "timeZone" => "UTC"
            ],
            "showAs" => "busy"
        ];

        $response = $client->patch($this->graph("me/events/{$eventId}"), [
            "headers" => [
                "Authorization" => "Bearer {$token}",
                "Content-Type" => "application/json"
            ],
            "json" => $body
        ]);

        return json_decode($response->getBody(), true);
    }

    // -----------------------------------------------------------
    // 8) Delete Event
    // -----------------------------------------------------------
    public function deletePlaceholderEvent($counselor, $eventId)
    {
        if (!$eventId) return;

        $token = $this->getActiveAccessToken($counselor);

        $client = $this->http();

        try {
            $client->delete($this->graph("me/events/{$eventId}"), [
                'headers' => [
                    'Authorization' => "Bearer {$token}"
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error("Outlook delete error: " . $e->getMessage());
        }
    }
}
