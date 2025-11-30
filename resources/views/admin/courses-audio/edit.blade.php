@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Course Audio</h5>
            <form action="{{ route('admin.courses-audio.update', $courseAudio->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.courses-audio._form')
            </form>
        </div>
    </div>
@endsection
