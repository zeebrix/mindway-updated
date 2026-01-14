@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="course_descriptionId" class="form-label">Course Type</label>
                    <select class="form-control" name="course_type">
                        <option value="audio">Audio</option>
                        <option value="video">Video</option>
                        <option value="article">Article</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">Course Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Course Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $course->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="course_duration" class="form-label">Course Duration (e.g., '8 Hours')</label>
                    <input type="text" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes) }}" required>
                </div>
                <div class="mb-3">
                    <label for="course_duration" class="form-label">Course Favorite Color</label>
                    <input type="text" class="form-control" id="theme_color" name="theme_color" value="{{ old('theme_color', $course->theme_color) }}" required>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thumbnail</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="course_thumbnail" class="form-label">Upload Thumbnail</label>
                    <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*">
                </div>
                @if ($course->thumbnail)
                <div class="mt-2">
                    <img src="{{ $course->thumbnail }}" alt="Current Thumbnail" class="img-fluid rounded" style="max-height: 150px;">
                    <p class="text-muted small mt-1">Current thumbnail</p>
                </div>
                @endif
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <button type="submit" class="btn btn-primary w-100">
                    {{ $course->exists ? 'Update Course' : 'Create Course' }}
                </button>
            </div>
        </div>
    </div>
</div>