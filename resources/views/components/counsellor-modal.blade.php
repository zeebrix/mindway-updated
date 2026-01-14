@props([
'id' => 'addCounsellorModal',
'title' => 'Add New Counsellor',
'action' => url('/manage-admin/add-counsellor'),
'locations' => [],
'languages' => [],
'timezones' => [],
'specializations' => [],
])

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ $action }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter your description..." required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="gender">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="communication_method">Communication Method</label>
                            <select class="form-select" id="communication_method" multiple name="communication_method[]" required>
                                <option value="Phone Call">Phone Call</option>
                                <option value="Video Call">Video Call</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="location">Select Location</label>
                            <select id="location" name="location" class="form-select select2">
                                <option value="">Select a location</option>
                                @foreach($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="language">Select Language</label>
                            <select id="language" name="language[]" class="form-select select2" multiple>
                                @foreach($languages as $lang)
                                <option value="{{ $lang }}">{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="specializations">Specializations</label>
                            <input type="text" id="tagsInput" name="tags[]" class="form-control" placeholder="Select Specialization" />
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="timezone">Timezone</label>
                            <select id="timezone" name="timezone" class="form-select">
                                <option value="">Select a timezone</option>
                                @foreach($timezones as $tz)
                                <option value="{{ $tz['name'] }}">{{ $tz['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary mindway-btn-blue" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary mindway-btn-blue">Save</button>
                </div>

            </form>
        </div>
    </div>
    <input type="hidden" id="specializationsJson"
       value='@json($specializations)' />
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/add-counselor-modal.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/dashboard/js/add-counselor-modal.js') }}"></script>
<script src="{{ asset('assets/dashboard/js/dropdown.js') }}"></script>
@endpush