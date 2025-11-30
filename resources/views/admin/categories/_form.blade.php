@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="name" class="form-label">Category Name</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select id="status" class="form-select" name="status" required>
        <option value="active" {{ old('status', $category->status ?? 'active') == 'active' ? 'selected' : '' }}>
            Active
        </option>
        <option value="inactive" {{ old('status', $category->status ?? '') == 'inactive' ? 'selected' : '' }}>
            Inactive
        </option>
    </select>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
