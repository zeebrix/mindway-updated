@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Home Emoji</h5>
            <form action="{{ route('admin.home-emojis.update', $homeEmoji->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.home_emojis._form', ['homeEmoji' => $homeEmoji])
            </form>
        </div>
    </div>
@endsection
