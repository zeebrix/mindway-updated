<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mindway</title>

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">

    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/vertical-layout-light/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/set-password.css') }}">

    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
</head>

<body>

    <div class="login-container">
        <!-- Left Section -->
        <div class="left-section">
            <img src="{{ asset('logo/loginLogo.png') }}" alt="Mindway Logo" class="login-illustration">
            <img src="{{ asset('logo/logo.png') }}" alt="Mindway Logo" class="brand-logo">
            <h1>Your All-in-One</h1>
            <h1>Platform for</h1>
            <h1>Employee Well-Being.</h1>
        </div>

        <!-- Form Section -->
        <div class="login-form">
            <img src="{{ asset('logo/logo.png') }}" alt="Mindway Logo" class="brand-logo">

            <h2 class="welcome-text">
                Hi {{ request('name') }} 👋
            </h2>

            <form method="POST" action="{{ url('set-password') }}" class="pt-3">
                @csrf

                <input type="hidden" name="id" value="{{ request('token') }}">
                <input type="hidden" name="email" value="{{ request('email') }}">
                <input type="hidden" name="type" value="{{ request('type') }}">

                <div class="form-group">
                    <label class="form-label fw-600">Set Password</label>
                    <input
                        type="password"
                        name="password"
                        minlength="8"
                        class="form-control form-control-lg password-input"
                        placeholder="*********"
                        required>
                </div>

                @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <button type="submit" class="btn update-btn">
                    Update
                </button>
            </form>
        </div>
    </div>

    <!-- Vendor JS -->
    <script src="{{ asset('vendors/js/vendor.bundle.base.js') }}"></script>

    <!-- App JS -->
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
    <script src="{{ asset('js/todolist.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('js/auth/set-password.js') }}"></script>

</body>

</html>