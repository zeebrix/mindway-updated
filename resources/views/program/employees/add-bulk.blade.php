

<div class="modal fade" id="addSessionModalBulk" tabindex="-1" aria-labelledby="addSessionModalLabelBulk" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h3 class="modal-title">Please upload a correctly formatted spreadsheet</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="dataFormBulk" method="POST" action="{{ route('admin.programs.add-bulk',$user->id) }}">
                @csrf

                <div class="modal-body">

                    <div class="title-row">
                        <b>Ensure there are no other column with other information</b>
                        <button type="submit" id="uploadDataBtn" class="btn btn-primary mindway-btn-blue" hidden>
                            Add Bulk List of Employee
                        </button>
                    </div>

                    <div id="preview-section">
                        <div class="table-responsive">
                            <table id="previewTable" class="table table-striped text-nowrap mb-0 align-middle">
                                <thead class="text-dark fs-4">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="previewTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>
                    <input type="hidden" name="program_detail_id" value="{{ $user?->programDetail?->id }}">
                    <input type="hidden" id="finalDataInput" name="finalData">

                    <div class="mb-3">
                        <label class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="uploadFile" required>
                    </div>

                    <button type="button" id="previewDataBtn" class="btn btn-primary mindway-btn">
                        Preview Data
                    </button>

                </div>
            </form>
        </div>
    </div>
</div>
