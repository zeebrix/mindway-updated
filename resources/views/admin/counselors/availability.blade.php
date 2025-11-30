@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
    {{-- All the styles from your original availability file go here --}}
    <style>...</style>

    <div class="card-body p-4">
        <h2 class="card-title mb-4" style="font-weight:700">My Availability for {{ $counselor->name }}</h2>
        {{-- The rest of the HTML from your original availability file goes here --}}
        {{-- ... --}}
    </div>
@endsection

@push('scripts')
    {{-- All the JavaScript from your original availability file goes here --}}
@endpush
