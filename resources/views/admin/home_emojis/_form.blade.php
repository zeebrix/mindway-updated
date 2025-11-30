@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

{{-- The 'type' field is handled automatically by the controller, so we don't need it in the form --}}

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $homeEmoji->name ?? '') }}" placeholder="e.g., Grateful" required>
</div>

<div class="mb-3">
    <label for="emoji" class="form-label">Home Emoji</label>
    <input type="text" class="form-control" id="emoji" name="emoji" value="{{ old('emoji', $homeEmoji->emoji ?? '') }}" placeholder="e.g., 🙏" required>
    <div class="form-text">You can copy and paste an emoji here.</div>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
