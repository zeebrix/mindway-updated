@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Edit Link</h5>
            <form action="{{ route('admin.links.update', $link->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.links._form', ['link' => $link])
            </form>
        </div>
    </div>
@endsection
