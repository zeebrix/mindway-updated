@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <div class="login-form">
        <img src="{{ asset('/logo/logo.png') }}" class="login-logo" alt="Mindway Logo">
        
        <h2 class="login-heading">
            Forgot Your Password?
        </h2>
        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form class="pt-3" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Address" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="mt-3">
                <button type="submit" class="btn btn-login">
                    Send Password Reset Link
                </button>
            </div>
            
            <div class="helper-links mt-3">
                <a href="{{ route('login') }}" class="forgot-password">Back to Login</a>
            </div>
        </form>
    </div>
@endsection
