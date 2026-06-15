<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0">Deceased Details</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Deceased Date *</label>
                <input type="date" class="form-control" name="deceased_date" data-required="true">
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Working Date *</label>
                <input type="date" class="form-control" name="last_working_date" data-required="true">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Login Disable Date *</label>
                <input type="date" class="form-control" name="login_disable_date" data-required="true">
            </div>
            <div class="col-md-6">
                <label class="form-label">Replacement Required *</label>
                <select class="form-select" name="replacement_required" data-required="true">
                    <option value="">Select</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Reason *</label>
                <select class="form-select" name="reason" data-required="true">
                    <option value="">Select Reason</option>
                    <option value="Natural Causes">Natural Causes</option>
                    <option value="Accident">Accident</option>
                    <option value="Illness">Illness</option>
                    <option value="Other">Other</option>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Explanation</label>
                <textarea class="form-control" name="explanation" rows="3"></textarea>
            </div>
        </div>

        <input type="hidden" name="employee_status" value="Deceased">
        <input type="hidden" name="exit_type" value="Deceased">
    </div>
</div>