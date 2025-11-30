@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Music</h5>
            <form action="{{ route('admin.music.update', $music->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.music._form', ['music' => $music])
            </form>
        </div>
    </div>
@endsection
