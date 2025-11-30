@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $link->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="sub_title" class="form-label">Sub-Title</label>
    <input type="text" class="form-control" id="sub_title" name="sub_title" value="{{ old('sub_title', $link->sub_title ?? '') }}">
</div>

<div class="mb-3">
    <label for="url_name" class="form-label">URL</label>
    <input type="text" class="form-control" id="url_name" name="url_name" value="{{ old('url_name', $link->url_name ?? '') }}" placeholder="e.g., /home or https://example.com" required>
</div>

<div class="mb-3 border p-3 rounded">
    <label class="form-label">Icon Type</label>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="icon_type" id="icon_type_class" value="class" checked onchange="toggleIconFields( )">
        <label class="form-check-label" for="icon_type_class">Icon Class (e.g., Font Awesome)</label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="icon_type" id="icon_type_upload" value="upload" onchange="toggleIconFields()">
        <label class="form-check-label" for="icon_type_upload">Upload Image</label>
    </div>

    <div id="icon_class_field" class="mt-2">
        <label for="icon_class" class="form-label">Font Awesome Class</label>
        <input type="text" class="form-control" id="icon_class" name="icon_class" placeholder="e.g., fas fa-home">
    </div>

    <div id="icon_upload_field" class="mt-2" style="display: none;">
        <label for="icon_upload" class="form-label">Icon Image</label>
        <input class="form-control" type="file" id="icon_upload" name="icon_upload" accept="image/*">
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">Submit</button>

@push('scripts')
<script>
    function toggleIconFields() {
        if (document.getElementById('icon_type_class').checked) {
            document.getElementById('icon_class_field').style.display = 'block';
            document.getElementById('icon_upload_field').style.display = 'none';
        } else {
            document.getElementById('icon_class_field').style.display = 'none';
            document.getElementById('icon_upload_field').style.display = 'block';
        }
    }
    // Run on page load to set initial state
    document.addEventListener('DOMContentLoaded', toggleIconFields);
</script>
@endpush
