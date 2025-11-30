@extends('layouts.app')

@section('selected_menu', 'active')

@section('content')
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Add Customer Success Manger</h5>
        <div>
            <div>
                <div class="alert alert-info">
                    <h6 class="fw-bold">🔒 Permissions & Restrictions</h6>
                    <ul class="mb-1">
                        <li><strong>Users Tab:</strong></li>
                        <ul>
                            <li>✅ Can view app users and their details</li>
                            <li>❌ Cannot delete any users from the platform</li>
                        </ul>
                        <li class="mt-2"><strong>Programs Tab:</strong></li>
                        <ul>
                            <li>✅ Can create and manage (edit/update) programs</li>
                            <li>✅ Can deactivate programs (soft disable)</li>
                            <li>❌ Cannot permanently delete any program</li>
                            <li>❌ Cannot delete or remove users from within a program</li>
                        </ul>
                    </ul>
                </div>

                <form action="{{route('admin.csm.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name"
                            aria-describedby="name" name="name" placeholder="Enter Name"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email"
                            aria-describedby="email" name="email"
                            placeholder="Enter Customer Success Email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password"
                            aria-describedby="password" name="password"
                            placeholder="Enter Password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            aria-describedby="password" name="password_confirmation"
                            placeholder="Enter Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection