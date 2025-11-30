@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add SOS Audio</h5>
            <form action="{{ route('admin.sos-audios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.sos_audios._form', ['sosAudio' => new \App\Models\SosAudio()])
            </form>
        </div>
    </div>
@endsection
