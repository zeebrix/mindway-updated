@extends('mw-1.layout.app')

@section('selected_menu', 'active')

@section('content')
<div class="row">
    <div class="col-10 offset-1">

        {{-- Alerts --}}
        @include('mw-1.components.alerts')

        <h3><b>Profile Setting of {{ $Counselor->name }}</b></h3>
        <br>

        <form action="{{ url('/profile-save') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ROLE & TIMEZONE --}}
            @include('mw-1.counsellor.sections.role-timezone', ['Counselor' => $Counselor])

            {{-- LOGO + GENDER --}}
            @include('mw-1.counsellor.sections.logo-gender', ['Counselor' => $Counselor])

            {{-- NOTICE PERIOD --}}
            @include('mw-1.counsellor.sections.notice-period', ['Counselor' => $Counselor])

            {{-- DESCRIPTION --}}
            @include('mw-1.counsellor.sections.description', ['Counselor' => $Counselor])

            {{-- SPECIALIZATION --}}
            @include('mw-1.counsellor.sections.specialization', ['Counselor' => $Counselor])

            {{-- LOCATION --}}
            @include('mw-1.counsellor.sections.location', ['Counselor' => $Counselor])

            {{-- LANGUAGE --}}
            @include('mw-1.counsellor.sections.language', ['Counselor' => $Counselor])

            {{-- GOOGLE CALENDAR --}}
            @include('mw-1.counsellor.sections.google-calendar', ['Counselor' => $Counselor])

            {{-- OUTLOOK CALENDAR --}}
            @include('mw-1.counsellor.sections.outlook-calendar', ['Counselor' => $Counselor])

            {{-- COMMUNICATION --}}
            @include('mw-1.counsellor.sections.communication', ['Counselor' => $Counselor])

            {{-- INTAKE LINK --}}
            @include('mw-1.counsellor.sections.intake-link', ['Counselor' => $Counselor])

            {{-- INTRO VIDEO --}}
            @include('mw-1.counsellor.sections.intro-video', ['Counselor' => $Counselor])

            <div class="text-center mt-4">
                <button type="submit" class="mindway-btn-blue btn btn-primary">Submit</button>
            </div>

        </form>
    </div>
</div>

{{-- TIMEZONE MODAL --}}
@include('mw-1.counsellor.modals.timezone')

@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/profile.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="{{ asset('assets/dashboard/js/profile.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/profile-dropdowns.js') }}"></script>
@endsection
