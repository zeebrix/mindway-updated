@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Music</h5>
            <form action="{{ route('admin.music.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.music._form', ['music' => new \App\Models\Music()])
            </form>
        </div>
    </div>
@endsection
