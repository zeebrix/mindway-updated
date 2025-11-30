@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Category Course</h5>
            <form action="{{ route('admin.category-courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.category_courses._form', ['categoryCourse' => new \App\Models\CategoryCourse()])
            </form>
        </div>
    </div>
@endsection
