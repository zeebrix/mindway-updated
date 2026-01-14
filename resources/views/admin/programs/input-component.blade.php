<div class="col-12 me-3 {{ $extraClass ?? '' }}" id="{{ $id }}">
    <div class="card custom-input-card">
        <div class="card-body custom-input-card-body p-4">
            <label for="{{ $name }}Id" class="form-label custom-input-label">
                {{ $label }}
            </label>
            <input type="{{ $type }}"
                class="form-control {{ $class??'' }} custom-input-field"
                id="{{ $name }}Id"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                @if ($is_required??'') required @endif
                value="{{ $value ?? '' }}">
            @if ($is_required??'')
            <div class="invalid-feedback">
                Please enter {{ strtolower($label) }}.
            </div>
            @endif
        </div>
    </div>
</div>