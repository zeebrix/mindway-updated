@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Home Emoji</h5>
            <form action="{{ route('admin.home-emojis.store') }}" method="POST">
                @csrf
                {{-- Pass a new Feeling model instance --}}
                @include('admin.home_emojis._form', ['homeEmoji' => new \App\Models\Feeling()])
            </form>
        </div>
    </div>
@endsection
