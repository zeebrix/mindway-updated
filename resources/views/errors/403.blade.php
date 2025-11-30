@extends('layouts.error')

@section('title', 'Access Denied')

@section('content')
<div class="container text-center my-5">
    <h1 class="display-4 text-danger">403 - Forbidden</h1>
    <p class="lead">Sorry, you don’t have permission to access this page.</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Go Home</a>
</div>
@endsection
