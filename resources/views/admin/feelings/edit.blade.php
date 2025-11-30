@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Feeling</h5>
            <form action="{{ route('admin.feelings.update', $feeling->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.feelings._form', ['feeling' => $feeling])
            </form>
        </div>
    </div>
@endsection
