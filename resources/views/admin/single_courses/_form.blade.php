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
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $singleCourse->title ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="subtitle" class="form-label">Subtitle</label>
            <input type="text" class="form-control" id="subtitle" name="subtitle" value="{{ old('subtitle', $singleCourse->subtitle ?? '') }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $singleCourse->duration ?? '') }}" placeholder="e.g., 10:30" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="color" class="form-label">Background Color</label>
                <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', $singleCourse->color ?? '#563d7c') }}" title="Choose your color">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="image" class="form-label">Cover Image</label>
            <input class="form-control" type="file" id="image" name="image" accept="image/*">
            @if (isset($singleCourse->id) && $singleCourse->image)
                <div class="mt-2"><img src="{{ Storage::url($singleCourse->image) }}" class="img-thumbnail" style="max-height: 150px;"></div>
            @endif
        </div>
        <div class="mb-3">
            <label for="single_audio" class="form-label">Audio File</label>
            <input class="form-control" type="file" id="single_audio" name="single_audio" accept="audio/*" {{ isset($singleCourse->id) ? '' : 'required' }}>
            @if (isset($singleCourse->id) && $singleCourse->single_audio)
                <div class="mt-2"><audio controls class="w-100"><source src="{{ Storage::url($singleCourse->single_audio) }}"></audio></div>
            @endif
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">Submit</button>
