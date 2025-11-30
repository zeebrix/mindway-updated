<?php

namespace App\Services;

use App\Models\CustomerDetail;
use App\Models\ProgramCustomer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected $brevoService;

    public function __construct(BrevoService $brevoService)
    {
        $this->brevoService = $brevoService;
    }

    public function createCustomer(array $data)
    {
        try {
            $user = Auth::user();


            $customer = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password'=> bcrypt('1234'),
                'user_type' => 'customer'
            ]);
            CustomerDetail::create([
                'user_id' => $user->id,
                'program_id' => $user->ProgramDetail->id,
                'level' => $data['level']
            ]);
            ProgramCustomer::create([
                'customer_id' => $customer->id,
                'program_id' => $user->id,
            ]);

            $this->brevoService->createContact($customer, $user);

            if ($data['level'] === 'admin') {
                $this->sendAdminEmail($customer, $user);
            }

            return $customer;
        } catch (\Exception $e) {
            Log::error('Failed to create customer: ' . $e->getMessage());
            throw $e; // Re-throw the exception to be handled by the controller
        }
    }

    protected function sendAdminEmail($customer, $program)
    {
        $recipient = $customer->email;
        $subject = 'You’ve Been Made an Admin for Mindway EAP';
        $template = 'emails.become-admin-member';
        $data = [
            'full_name' => $customer->name ?? '',
            'company_name' => $program->company_name ?? '',
            'access_code' => $program->code ?? ''
        ];
        return ;
        // Assuming sendDynamicEmailFromTemplate is a helper function
        // sendDynamicEmailFromTemplate($recipient, $subject, $template, $data);
    }
}
