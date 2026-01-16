<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterVerificationRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerLoginRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerUpdateProfileRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerForgetPasswordRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerResetPasswordVerificationRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerGetNotifyRequest;
use App\Http\Requests\API\MobileApp\Auth\Customer\CustomerRegisterByEmailRequest;
use App\Http\Requests\FindMeRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Repositories\CustomersRepository;
use App\Services\CustomerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerController extends Controller
{

    protected $customerService;
    protected $repository;
    public function __construct(CustomerService $customerService, CustomersRepository $repository)
    {
        $this->customerService = $customerService;
        $this->repository = $repository;
    }
    public function passwordReset(PasswordResetRequest $request)
    {
        return $this->customerService->passwordReset($request->validated());
    }
    public function findMe(FindMeRequest $request)
    {
        return $this->customerService->findMe($request->validated());
    }
    public function verifyOTP(VerifyOtpRequest $request)
    {
        return $this->customerService->verifyOTP($request->validated());
    }

    public function register(CustomerRegisterRequest $request)
    {
        return $this->customerService->store($request->validated());
    }
    public function registerByEmail(CustomerRegisterByEmailRequest $request)
    {
        return $this->customerService->store($request->validated());
    }
    public function verifySignup(CustomerRegisterVerificationRequest $request)
    {
        return $this->customerService->verifySignup($request->validated());
    }
    public function login(CustomerLoginRequest $request)
    {
        return $this->customerService->login($request->validated());
    }
    public function getNotify(CustomerGetNotifyRequest $request)
    {
        return $this->customerService->updateNotify($request->validated());
    }
    public function updateProfile(CustomerUpdateProfileRequest $request)
    {
        return $this->customerService->updateProfile($request);
    }
}
