@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Category</h5>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                @include('admin.categories._form', ['category' => new \App\Models\Category()])
            </form>
        </div>
    </div>
@endsection
