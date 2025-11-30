@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="category_id" class="form-label">Category</label>
    <select id="category_id" class="form-select" name="category_id" required>
        <option value="">-- Select a Category --</option>
        @foreach ($categories as $id => $name)
            <option value="{{ $id }}" {{ old('category_id', $categoryCourse->category_id ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="title" class="form-label">Course Title</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $categoryCourse->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $categoryCourse->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="thumbnail" class="form-label">Thumbnail</label>
    <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*">
    @if (isset($categoryCourse->id) && $categoryCourse->thumbnail)
        <div class="mt-2">
            <img src="{{ Storage::url($categoryCourse->thumbnail) }}" alt="Current Thumbnail" class="img-thumbnail" style="max-height: 150px;">
        </div>
    @endif
</div>

<button type="submit" class="btn btn-primary">Submit</button>
