@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
    {{-- All the styles from your original profile file go here --}}
    <style>...</style>

    <div class="row">
        <div class="col-10 offset-1">
            @if (session()->has('message'))<div class="alert alert-success">{{ session('message') }}</div>@endif
            @if ($errors->any())
                <div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <h3><b>Profile Setting of {{ $counselor->name }}</b></h3>  

            
            {{-- The form now points to the conventional update route --}}
            <form action="{{ route('admin.counselors.update', $counselor->id) }}" id="profile_form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- This is the exact HTML structure from your original profile file --}}
                {{-- It will work perfectly here --}}
                <div class="row">
                    <div class="col-3 me-3"><div class="card" style="border-radius: 20px">...</div></div>
                    <div class="col-6"><div class="card" style="border-radius: 20px">...</div></div>
                </div>
                {{-- ... and so on for all other fields and sections ... --}}

                <div class="text-center mt-4">
                    <button type="submit" class="mindway-btn-blue btn btn-primary">Save Profile Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- All the JavaScript from your original profile file goes here --}}
@endpush
