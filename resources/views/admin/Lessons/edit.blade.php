@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit Lesson: {{ $lesson->title }}</h1>

    <form action="{{ route('admin.lessons.update', [
        'course' => $course_id,
        'lesson' => $lesson->id
    ]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.lessons._form', ['lesson' => $lesson])
    </form>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/dashboard/js/lesson-form.js') }}"></script>
@endpush