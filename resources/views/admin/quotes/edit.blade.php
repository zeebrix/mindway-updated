@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Quote</h5>
            <form action="{{ route('admin.quotes.update', $quote->id) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.quotes._form', ['quote' => $quote])
            </form>
        </div>
    </div>
@endsection
