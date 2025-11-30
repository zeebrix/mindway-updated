@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="login-form">
        <img src="{{ asset('/logo/logo.png') }}" class="login-logo" alt="Mindway Logo">
        
        <h2 class="login-heading">
            Reset Your Password
        </h2>

        <form class="pt-3" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Address" value="{{ $email ?? old('email') }}" required autofocus>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-control form-control-lg" placeholder="New Password" required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm New Password" required>
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-login">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
@endsection
