@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add Course Audio</h5>
            <form action="{{ route('admin.courses-audio.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.courses-audio._form', ['courseAudio' => new \App\Models\CourseAudio()])
            </form>
        </div>
    </div>
@endsection
