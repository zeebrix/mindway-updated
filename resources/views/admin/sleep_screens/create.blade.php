@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Sleep Screen Audio</h5>
            <form action="{{ route('admin.sleep-screens.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.sleep_screens._form', ['sleepScreen' => new \App\Models\SleepScreen()])
            </form>
        </div>
    </div>
@endsection
