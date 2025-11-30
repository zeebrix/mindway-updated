@extends('layouts.error')

@section('title', 'Page Not Found')

@section('content')
<div class="container text-center my-5">
    <h1 class="display-4 text-warning">404 - Page Not Found</h1>
    <p class="lead">The page you’re looking for doesn’t exist or has been moved.</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Return Home</a>
</div>
@endsection
