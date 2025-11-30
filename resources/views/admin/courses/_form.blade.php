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
                    <label for="course_title" class="form-label">Course Title</label>
                    <input type="text" class="form-control" id="course_title" name="course_title" value="{{ old('course_title', $course->course_title) }}" required>
                </div>

                <div class="mb-3">
                    <label for="course_description" class="form-label">Course Description</label>
                    <textarea class="form-control" id="course_description" name="course_description" rows="5">{{ old('course_description', $course->course_description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="course_duration" class="form-label">Course Duration (e.g., '8 Hours')</label>
                    <input type="text" class="form-control" id="course_duration" name="course_duration" value="{{ old('course_duration', $course->course_duration) }}" required>
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
                    <input class="form-control" type="file" id="course_thumbnail" name="course_thumbnail" accept="image/*">
                </div>
                @if ($course->course_thumbnail)
                    <div class="mt-2">
                        <img src="{{ $course->course_thumbnail }}" alt="Current Thumbnail" class="img-fluid rounded" style="max-height: 150px;">
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
