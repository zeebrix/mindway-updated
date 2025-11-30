@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Single Course</h5>
            <form action="{{ route('admin.single-courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.single_courses._form', ['singleCourse' => new \App\Models\SingleCourse()])
            </form>
        </div>
    </div>
@endsection
