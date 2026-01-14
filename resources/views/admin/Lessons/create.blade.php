@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Add New Lesson</h1>

    <form action="{{ route('admin.lessons.store',$courseId) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.lessons._form', ['lesson' => $lesson])
    </form>
</div>

@endsection
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script src="{{ asset('assets/dashboard/js/lesson-form.js') }}"></script>
@endpush