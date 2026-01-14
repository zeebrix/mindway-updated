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
    {{-- Left Column: Lesson Details --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">

                {{-- Lesson Type --}}
                <div class="mb-3">
                    <label class="form-label">Lesson Type</label>
                    <select name="lesson_type" id="lesson_type" class="form-control" required>
                        <option value="">Select type</option>
                        <option value="audio" {{ old('lesson_type', $lesson->lesson_type) == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="video" {{ old('lesson_type', $lesson->lesson_type) == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="article" {{ old('lesson_type', $lesson->lesson_type) == 'article' ? 'selected' : '' }}>Article</option>
                    </select>
                </div>

                {{-- Title --}}
                <div class="mb-3">
                    <label class="form-label">Lesson Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $lesson->title) }}" required>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $lesson->description) }}</textarea>
                </div>

                {{-- Duration --}}
                <div class="mb-3">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="text" name="duration_minutes" class="form-control" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" required>
                </div>

                {{-- Order --}}
                <div class="mb-3">
                    <label class="form-label">Order No</label>
                    <input type="number" name="order_no" class="form-control" value="{{ old('order_no', $lesson->order_no) }}">
                </div>

                {{-- Host / Author --}}
                <div class="mb-3">
                    <label class="form-label">Host Name</label>
                    <input type="text" name="host_name" class="form-control" value="{{ old('host_name', $lesson->host_name) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Author Name</label>
                    <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $lesson->author_name) }}">
                </div>

                {{-- AUDIO --}}
                <div class="mb-3 lesson-field" id="audio-field">
                    <label class="form-label">Audio File</label>
                    <input type="file" name="audio" class="form-control">
                    @if($lesson->audio)
                    <small class="text-muted d-block mt-1">Existing file: {{ basename($lesson->audio) }}</small>
                    @endif
                </div>

                {{-- VIDEO --}}
                <div class="mb-3 lesson-field" id="video-field">
                    <label class="form-label">Video URL</label>
                    <input type="url" name="video" class="form-control" value="{{ old('video', $lesson->video) }}">
                </div>

                {{-- ARTICLE --}}
                <div class="mb-3 lesson-field" id="article-field">
                    <label class="form-label">Article Text</label>
                    <textarea id="article_text" name="article_text" class="form-control" rows="6">{{ old('article_text', $lesson->article_text) }}</textarea>
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary w-100">
            {{ $lesson->exists ? 'Update Lesson' : 'Create Lesson' }}

    </div>