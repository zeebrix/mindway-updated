@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Sleep Audio</h5>
            <form action="{{ route('admin.sleep-audios.update', $sleepAudio->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.sleep_audios._form', ['sleepAudio' => $sleepAudio])
            </form>
        </div>
    </div>
@endsection
