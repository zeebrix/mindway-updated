@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Add New Link</h5>
            <form action="{{ route('admin.links.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.links._form', ['link' => new \App\Models\Link()])
            </form>
        </div>
    </div>
@endsection
