<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Models\OutlookToken;
use App\Services\OutlookService;
use Illuminate\Http\Request;
use Auth;

class OutlookController extends Controller
{
    protected $outlook;

    public function __construct(OutlookService $outlook)
    {
        $this->outlook = $outlook;
    }

    /** Redirect to Microsoft login */
    public function connect(Request $request)
    {
        $state = json_encode([
            'counsellor_id' => $request->id,
        ]);
        $params = http_build_query([
            'client_id'     => env('OUTLOOK_CLIENT_ID'),
            'response_type' => 'code',
            'redirect_uri'  => env('OUTLOOK_REDIRECT_URI'),
            'response_mode' => 'query',
            'scope'         => 'openid profile email offline_access User.Read Calendars.Read Calendars.ReadWrite MailboxSettings.Read',
            'state'         => base64_encode($state),
        ]);

        return redirect("https://login.microsoftonline.com/" . env('OUTLOOK_TENANT_ID') . "/oauth2/v2.0/authorize?" . $params);
    }

    /** Callback: store tokens */
    public function callback(Request $request)
    {
        try {
            $state = json_decode(base64_decode($request->state), true);

            $counsellorId = $state['counsellor_id'];

            // Fetch the counsellor
            $tokens = $this->outlook->getTokens($request->code);
            $userInfo = $this->outlook->getUserInfo($tokens['access_token']);
            $counsellor = Counselor::Where('id', $counsellorId)->first();
            $counsellor->outlook_id = $userInfo['id'];
            $counsellor->outlook_name = $userInfo['displayName'];
            $counsellor->outlook_gmail = $userInfo['mail'];
            $counsellor->outlook_picture = null;
            $counsellor->save();
            OutlookToken::updateOrCreate(
                ['counseller_id' => $counsellorId],
                [
                    'access_token'  => encrypt($tokens['access_token']),
                    'refresh_token' => encrypt($tokens['refresh_token']),
                    'expires_in'    => now()->addSeconds($tokens['expires_in']),
                ]
            );

            if (auth()->check()) {
                return redirect()->route('admin.counsellor.profile', $counsellorId);
            }
            return redirect()->route('counseller.profile');
        } catch (\Throwable $th) {
            dd($th->getMessage());
            if (auth()->check()) {
                return redirect()->route('admin.counsellor.profile', $counsellorId);
            }
            return redirect()->route('counseller.profile');
        }
    }
}
