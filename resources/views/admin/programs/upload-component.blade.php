<div class="col-12 me-3">
    <div class="card upload-logo-card">
        <div class="d-flex align-items-center">
            <label for="{{ $id }}Id" class="form-label mb-1 upload-logo-label">{{ $label }}</label>
            <input type="file"
                class="form-control"
                id="{{ $id }}Id"
                name="{{ $name }}"
                @if(!empty($required)) required @endif>
            <div class="invalid-feedback">Please upload a logo.</div>
            <div>
                <img id="{{ $id }}Preview"
                    class="upload-logo-preview"
                    src="{{ !empty($existing_image) ? asset('storage/'.$existing_image) : asset('/images/upload.png') }}"
                    alt="uploaded image">
            </div>
        </div>
    </div>
</div>