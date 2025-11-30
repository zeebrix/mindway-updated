<?php

namespace App\Services;

use GuzzleHttp\Client;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Model\CreateContact;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('BREVO_API_KEY'));
        $this->apiInstance = new ContactsApi(new Client(), $config);
    }

    public function createContact($customer, $program)
    {
        try {
            $createContact = new CreateContact([
                'email' => $customer->email,
                'attributes' => (object) [
                    'EMAIL' => $customer->email,
                    'FIRSTNAME' => $customer->name,
                    'CODEACCESS' => $program->code,
                    'COMPANY' => $program->company_name,
                    'MS' => $program->max_session,
                    'LASTNAME' => ""
                ],
                'listIds' => [9],
            ]);

            $this->apiInstance->createContact($createContact);
        } catch (\Exception $e) {
            Log::error('Failed to create Brevo contact: ' . $e->getMessage());
            // Decide if you want to throw the exception or just log it
        }
    }
}
