@extends('layouts.error')

@section('title', 'Server Error')

@section('content')
<div class="container text-center my-5">
    <h1 class="display-4 text-danger">500 - Server Error</h1>
    <p class="lead">Oops! Something went wrong on our end.</p>
    <a href="{{ route('home') }}" class="btn btn-primary mt-3">Back to Home</a>
</div>
@endsection
