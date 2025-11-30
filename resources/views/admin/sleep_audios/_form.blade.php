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
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $sleepAudio->title ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Related Course</label>
            <select id="course_id" class="form-select" name="course_id" required>
                <option value="">-- Select a Course --</option>
                @foreach ($courses as $id => $name)
                    <option value="{{ $id }}" {{ old('course_id', $sleepAudio->course_id ?? '') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $sleepAudio->description ?? '') }}</textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $sleepAudio->duration ?? '') }}" placeholder="e.g., 25:10" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="color" class="form-label">Background Color</label>
                <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', $sleepAudio->color ?? '#563d7c') }}" title="Choose your color">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/*">
            @if (isset($sleepAudio->id) && $sleepAudio->image)
                <div class="mt-2"><img src="{{ Storage::url($sleepAudio->image) }}" class="img-thumbnail" style="max-height: 150px;"></div>
            @endif
        </div>
        <div class="mb-3">
            <label for="audio" class="form-label">Audio File</label>
            <input class="form-control" type="file" id="audio" name="audio" accept="audio/*" {{ isset($sleepAudio->id) ? '' : 'required' }}>
            @if (isset($sleepAudio->id) && $sleepAudio->audio)
                <div class="mt-2"><audio controls class="w-100"><source src="{{ Storage::url($sleepAudio->audio) }}"></audio></div>
            @endif
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">Submit</button>
