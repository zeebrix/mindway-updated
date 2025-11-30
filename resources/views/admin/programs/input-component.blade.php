<div class="col-12 me-3 {{ $extraClass ?? '' }}" id="{{ $id }}">
    <div class="card custom-input-card">
        <div class="card-body custom-input-card-body p-4">
            <div class="d-flex">
                <label for="{{ $name }}Id" class="form-label custom-input-label">
                    {{ $label }}
                </label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="col-11">
                    <input type="{{ $type }}"
                           class="form-control {{ $class??'' }} custom-input-field"
                           id="{{ $name }}Id"
                           aria-describedby="{{ $name }}Help"
                           name="{{ $name }}"
                           placeholder="{{ $placeholder }}"
                           @if ($is_required??'') required @endif
                           value="{{ $value ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>
