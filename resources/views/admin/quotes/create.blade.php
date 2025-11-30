@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Quote</h5>
            <form action="{{ route('admin.quotes.store') }}" method="POST">
                @csrf
                @include('admin.quotes._form', ['quote' => new \App\Models\Quote()])
            </form>
        </div>
    </div>
@endsection
