@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="course_id" class="form-label">Choose Course</label>
    <select id="course_id" class="form-select" name="course_id" required>
        <option value="">-- Select a Course --</option>
        @foreach ($courses as $id => $title)
            <option value="{{ $id }}" {{ old('course_id', $courseAudio->course_id ?? '') == $id ? 'selected' : '' }}>
                {{ $title }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="audio_title" class="form-label">Audio Title</label>
    <input type="text" class="form-control" id="audio_title" name="audio_title" value="{{ old('audio_title', $courseAudio->audio_title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="duration" class="form-label">Duration</label>
    <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $courseAudio->duration ?? '') }}" placeholder="e.g., 15:30" required>
</div>

<div class="mb-3">
    <label for="audio" class="form-label">Audio File (MP3, WAV, OGG)</label>
    <input type="file" class="form-control" id="audio" name="audio" {{ isset($courseAudio) ? '' : 'required' }}>
    @if (isset($courseAudio) && $courseAudio->audio_path)
        <div class="mt-2">
            <p>Current Audio:</p>
            <audio controls>
                <source src="{{ Storage::url($courseAudio->audio_path) }}" type="audio/mpeg">
            </audio>
        </div>
    @endif
</div>

<button type="submit" class="btn btn-primary">Submit</button>
