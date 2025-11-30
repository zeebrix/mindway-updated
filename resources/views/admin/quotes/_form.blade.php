@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label for="name" class="form-label">Quote Text</label>
    <textarea class="form-control" id="name" name="name" rows="5" required>{{ old('name', $quote->name ?? '') }}</textarea>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
