@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Feeling</h5>
            <form action="{{ route('admin.feelings.store') }}" method="POST">
                @csrf
                @include('admin.feelings._form', ['feeling' => new \App\Models\Feeling()])
            </form>
        </div>
    </div>
@endsection
