<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Mindway')</title>
    <link rel="stylesheet" href="{{ asset('/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/css/layout.css') }}">
     @yield('css')
</head>

<body>
    <div class="login-container">
        @yield('content')
    </div>
    <script src="{{ asset('/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('/js/off-canvas.js') }}"></script>
    <script src="{{ asset('/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('/js/template.js') }}"></script>
    <script src="{{ asset('/assets/js/login-page.js') }}"></script>
    <!-- <script src="https://www.google.com/recaptcha/api.js?render={{ env('NOCAPTCHA_SITEKEY' ) }}"></script> -->
</body>

</html>