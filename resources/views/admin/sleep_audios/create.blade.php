@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Sleep Audio</h5>
            <form action="{{ route('admin.sleep-audios.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.sleep_audios._form', ['sleepAudio' => new \App\Models\SleepAudio()])
            </form>
        </div>
    </div>
@endsection
