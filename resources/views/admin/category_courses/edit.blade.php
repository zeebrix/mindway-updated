@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Category Course</h5>
            <form action="{{ route('admin.category-courses.update', $categoryCourse->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.category_courses._form', ['categoryCourse' => $categoryCourse])
            </form>
        </div>
    </div>
@endsection
