@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Single Course</h5>
            <form action="{{ route('admin.single-courses.update', $singleCourse->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.single_courses._form', ['singleCourse' => $singleCourse])
            </form>
        </div>
    </div>
@endsection
