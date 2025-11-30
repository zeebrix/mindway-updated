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
            <label for="audio_title" class="form-label">Audio Title</label>
            <input type="text" class="form-control" id="audio_title" name="audio_title" value="{{ old('audio_title', $sleepScreen->audio_title ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $sleepScreen->duration ?? '') }}" placeholder="e.g., 04:20" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Background Image</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/*">
            @if (isset($sleepScreen->id) && $sleepScreen->image)
                <div class="mt-2"><img src="{{ Storage::url($sleepScreen->image) }}" class="img-thumbnail" style="max-height: 150px;"></div>
            @endif
        </div>
        <div class="mb-3">
            <label for="sleep_audio" class="form-label">Audio File</label>
            <input class="form-control" type="file" id="sleep_audio" name="sleep_audio" accept="audio/*" {{ isset($sleepScreen->id) ? '' : 'required' }}>
            @if (isset($sleepScreen->id) && $sleepScreen->sleep_audio)
                <div class="mt-2"><audio controls class="w-100"><source src="{{ Storage::url($sleepScreen->sleep_audio) }}"></audio></div>
            @endif
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">Submit</button>
