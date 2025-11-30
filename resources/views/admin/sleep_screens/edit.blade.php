@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Sleep Screen Audio</h5>
            <form action="{{ route('admin.sleep-screens.update', $sleepScreen->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.sleep_screens._form', ['sleepScreen' => $sleepScreen])
            </form>
        </div>
    </div>
@endsection
