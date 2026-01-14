@extends('layouts.app')

@section('selected_menu', 'active')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<link rel="stylesheet" href="{{ asset('assets/dashboard/counselor/css/profile.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-10 offset-1">

        {{-- Alerts --}}
        @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
        @endif

        <h3><b>Profile Setting of {{ $counselor->name }}</b></h3>
        <br>

        <form action="{{ route('counsellor.profile.save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="counselor_id" name="counselor_id" value="{{$user->id}}">

            {{-- Top Cards: Role & Timezone --}}
            <div class="row mb-3">
                <div class="col-3 me-3">
                    <div class="card upload-card">
                        <div class="card-body d-flex flex-column justify-content-center p-4">
                            <p class="mb-1">My Role</p>
                            <h5>Counsellor</h5>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="card upload-card">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="d-flex flex-column">
                                <p class="mb-0">Time Zone</p>
                                <b id="selected-timezone">{{ $counselor->timezone }}</b>
                            </div>
                            <a href="#" class="timezone-link" data-bs-toggle="modal" data-bs-target="#timezoneModal">change</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Upload Logo & Gender --}}
            <div class="row mb-3">
                <div class="col-3">
                    <div class="card upload-card" id="uploadLogoTrigger">
                        <div class="card-body d-flex align-items-center p-4 upload-trigger">
                            <div class="upload-text">
                                <input type="file" id="uploadLogoInput" />
                                <h5>Upload Logo</h5>
                            </div>
                            @if($counselor->avatar)
                            <img src="{{ asset('storage/'.$counselor->avatar) }}" class="upload-icon" alt="logo image">
                            @else
                            <div>
                                <img src="{{ asset('/assets/images/upload.png') }}" class="upload-icon" alt="logo image">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-7">
                    <div class="card gender-card">
                        <div class="card-body gender-body">
                            <div class="gender-wrapper">
                                <strong>Gender</strong>
                                @php
                                $genders = ['Male','Female','Other'];
                                @endphp
                                @foreach($genders as $g)
                                <div class="btn-group">
                                    <input type="radio" class="btn-check" name="gender" id="{{ strtolower($g) }}" value="{{ $g }}" autocomplete="off" @if($counselor->gender == $g) checked @endif>
                                    <label class="btn btn-outline-primary gender-btn" for="{{ strtolower($g) }}">{{ $g }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notice Period --}}
            <div class="col-4 mb-3">
                <div class="card notice-card">
                    <div class="card-body notice-body">
                        <label for="notice_periodId">Notice Period</label>
                        <div class="notice-input-wrapper">
                            <input type="number" class="form-control notice-input" id="notice_periodId" name="notice_period" placeholder="Enter Hours" value="{{ $counselor->notice_period }}">
                            <h4 class="notice-unit">hours</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4">
                        <label for="description">My Counsellor Page Description</label>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="col-11">
                                <textarea id="description" name="description" rows="5" class="form-control description-box" readonly>{{ $counselor->description }}</textarea>
                            </div>
                            <div class="col-1" id="edit-description">
                                <i class="ti ti-pencil"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Specializations, Location, Languages --}}
            @php
            $specialization = json_decode($counselor->specialization, true);
            $language = $counselor->language;
            $location = $counselor->location;
            @endphp
            <input type="hidden" id="default-location" value="{{$location}}">
            <input type="hidden" id="default-languages" value="{{$language}}">
            <input type="hidden" id="default-specializations" value='@json($specialization)'>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4">
                        <label for="tagsInput">Select Specialization:</label>
                        <input type="text" id="tagsInput" name="tags" class="form-control" placeholder="Select Specialization" />
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4">
                        <label for="location">Select Location:</label>
                        <select id="location" class="form-control select2" name="location">
                            <option value="">Select a location</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body p-4">
                        <label for="language">Select Language:</label>
                        <select id="language" class="form-control select2" multiple="multiple" name="language[]">
                            <option value="">Select a language</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Calendar Links --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card p-4">
                        <h5>Link Mindway Calendar</h5>
                        <p>Link Mindway Calendar to prevent double booking</p>
                        <a href="{{ route('auth.google.redirect', ['id'=>$counselor->id]) }}">Link to my Mindway Google Calendar</a>
                        <br><span>{{ $counselor->google_id }}</span> | <span>{{ $counselor->google_name }}</span>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="card p-4">
                        <h5>Cross Check Personal Outlook Calendar</h5>
                        <p>Use this if you have a personal outlook calendar you want Mindway to check and avoid double bookings.</p>
                        <a href="{{ route('auth.outlook.connect', ['id'=>$counselor->id]) }}">Link to my Mindway OutLook Calendar</a>
                        <br><span>{{ $counselor?->outlook_id }}</span> | <span>{{ $counselor?->outlook_name }}</span>
                    </div>
                </div>
            </div>

            {{-- Communication Methods --}}
            <div class="col-8 mb-3">
                <div class="card p-4">
                    <h5>Select Your Preferred Communication Method</h5>
                    <div class="d-flex flex-wrap mt-4">
                        @php
                        $methods = ['Phone Call','Video Call'];
                        $selectedMethods = json_decode($counselor->communication_method) ?? [];
                        @endphp
                        @foreach($methods as $method)
                        <div class="btn-group">
                            <input type="checkbox" class="btn-check" name="communication_methods[]" id="{{ str_replace(' ','-',strtolower($method)) }}" value="{{ $method }}" autocomplete="off" @if(in_array($method,$selectedMethods)) checked @endif>
                            <label class="btn btn-outline-primary rounded-pill px-4" for="{{ str_replace(' ','-',strtolower($method)) }}">{{ $method }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Intake Link --}}
            <div class="col-12 mb-3">
                <div class="card p-4">
                    <label for="intake_linkId">Intake Link</label>
                    <input type="url" class="form-control" id="intake_linkId" name="intake_link" placeholder="Enter Intake Link" value="{{ $counselor->intake_link }}">
                </div>
            </div>

            {{-- Upload Intro Video --}}
            <div class="row mb-3">
                <div class="col-6">
                    <div class="card upload-card" id="uploadIntroTrigger">
                        <div class="card-body upload-trigger">
                            <div class="upload-text">
                                <input type="file" id="uploadIntroInput" accept="video/*" />
                                <h5>Upload Intro Video</h5>
                            </div>
                            <div id="videoPreviewContainer">
                                <img id="videoThumbnail" src="{{ asset('/assets/images/play.png') }}" class="upload-icon" alt="video thumbnail">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    @if($counselor->introduction_video)
                    <video width="200" height="200" controls>
                        <source src="{{ asset('storage/'.$counselor->introduction_video) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    @endif
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="text-center">
                <button type="submit" class="mindway-btn-blue btn btn-primary">Submit</button>
            </div>
        </form>

        {{-- Timezone Modal --}}
        <div class="modal fade" id="timezoneModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Select Time Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="timezone-search">
                            <input type="text" id="timezone-search" class="form-control" placeholder="Search time zones...">
                        </div>
                        <div id="timezone-list" class="timezone-list"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="{{ asset('assets/dashboard/counselor/js/profile.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/dropdown.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/availability.js') }}"></script>

@endpush