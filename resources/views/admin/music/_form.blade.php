@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $music->title ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="subtitle" class="form-label">Subtitle</label>
            <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $music->subtitle ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $music->duration ?? '') }}" placeholder="e.g., 03:45" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Cover Image</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/*">
            @if (isset($music->id) && $music->image)
                <div class="mt-2"><img src="{{ Storage::url($music->image) }}" class="img-thumbnail" style="max-height: 150px;"></div>
            @endif
        </div>
        <div class="mb-3">
            <label for="music_audio" class="form-label">Audio File</label>
            <input class="form-control" type="file" id="music_audio" name="music_audio" accept="audio/*" {{ isset($music->id) ? '' : 'required' }}>
            @if (isset($music->id) && $music->music_audio)
                <div class="mt-2"><audio controls class="w-100"><source src="{{ Storage::url($music->music_audio) }}"></audio></div>
            @endif
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">Submit</button>
