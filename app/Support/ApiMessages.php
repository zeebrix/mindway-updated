<?php

namespace App\Support;

class ApiMessages
{
    // Auth
    public const LOGIN_SUCCESS = 'Login successfully.';
    public const INVALID_CREDENTIALS = 'Password or email incorrect. If you’re still having trouble, reset your password.';
    public const ACCOUNT_NOT_CONFIGURED = 'This account is not set up correctly.';
    public const UNAUTHORIZED = 'Unauthorized access.';
    public const FORBIDDEN = 'You are not allowed to perform this action.';
    public const SERVER_ERROR = 'Something went wrong. Please try again later.';
    public const EMAIL_ALREADY_REGISTERED = 'This email is already registered. Please try a different one.';
    public const OTP_SENT = 'OTP sent to the provided email.';
    public const EMAIL_NOT_FOUND = 'No email found. Please try another email or use your access code below.';
    public const OTP_EMAIL_FAILED = 'An error occurred while sending the OTP email.';
    public const OTP_VERIFIED = 'OTP verified successfully.';
    public const INVALID_OTP = 'Invalid OTP.';
    public const OTP_EXPIRED = 'OTP not found or has expired.';
    public const CUSTOMER_REGISTERED = 'Customer registered successfully.';
    public const PROFILE_UPDATED = 'Profile updated successfully.';
    public const PROFILE_UPDATE_FAILED = 'Profile unable to update. Something went wrong.';
    public const PASSWORD_RESET_LINK_SENT = 'Password reset link has been sent to your email.';
    public const ACCOUNT_NOT_FOUND = 'No account found with this email address.';
    public const PASSWORD_RESET_FAILED = 'Unable to send password reset email. Please try again later.';
    public const CUSTOMER_VERIFIED = 'Customer verified successfully.';
    public const INVALID_VERIFICATION = 'Phone number or verification code is invalid.';
    public const NOTIFY_UPDATED = 'Notification time and day updated successfully.';
    public const MISSING_EMAIL = 'Email is required.';
}
