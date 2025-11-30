@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit SOS Audio</h5>
            <form action="{{ route('admin.sos-audios.update', $sosAudio->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.sos_audios._form', ['sosAudio' => $sosAudio])
            </form>
        </div>
    </div>
@endsection
