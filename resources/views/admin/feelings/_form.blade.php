@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="name" class="form-label">Feeling Name</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $feeling->name ?? '') }}" placeholder="e.g., Happy" required>
</div>

<div class="mb-3">
    <label for="emoji" class="form-label">Emoji</label>
    <input type="text" class="form-control" id="emoji" name="emoji" value="{{ old('emoji', $feeling->emoji ?? '') }}" placeholder="e.g., 😊" required>
    <div class="form-text">You can copy and paste an emoji here.</div>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
