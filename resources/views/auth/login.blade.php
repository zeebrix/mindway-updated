@extends('layouts.auth')

@section('title', 'Login')
@section('css')
<link rel="stylesheet" href="{{ asset('/assets/css/login-page.css') }}">
@endsection

@section('content')
<x-auth-panel
    title="Your All-in-One"
    subtitle="Platform for"
    tagline="Employee Well-Being." />
<div class="login-form">
    <img src="{{ asset('/logo/logo.png') }}" class="login-logo" alt="Mindway Logo">

    <h2 class="login-heading">
        Log in
        @if(request('portal') == 'admin')
        to Admin Portal
        @elseif(request('portal') == 'program')
        to Employer Portal
        @elseif(request('portal') == 'counsellor')
        to Counsellor Portal
        @endif
    </h2>

    <form class="pt-3" id="login-form" method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <div class="form-group">
            <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" value="{{ old('email') }}" required>
            @error('email')
            <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" name="password" class="form-control form-control-lg" placeholder="Password" required>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-login" id="login-button">
                <span id="login-text">Login</span>
                <span id="login-spinner" class="login-spinner">
                    <i class="fa fa-spinner fa-spin"></i> Loading...
                </span>
            </button>
        </div>

        <div class="helper-links">
            <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>

            @if(request('portal') == 'program')
            <span class="signup-text">Don’t have an account? </span>
            <a href="" class="signup-link">Sign Up</a>
            @endif
        </div>
    </form>
</div>
@endsection