@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Policies');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">


 @if(isset($permissions) && $permissions->can_create)
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_policy">
                    Add Policy
                </a>
            </div>
            @endif
    
    <!-- =====================================================
         ZOHO STYLE TABS (ADDED)
    ====================================================== -->
    <ul class="nav leave-tabs mb-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#policies-tab">Policies</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#logs-tab">Activity Log</a>
        </li>
    </ul>

    


    <!-- =====================================================
         TAB CONTENT AREA
    ====================================================== -->
    <div class="tab-content pt-4">


        <!-- =====================================================
             TAB 1 : POLICIES LIST  (Your original table)
        ====================================================== -->
        <div class="tab-pane fade show active" id="policies-tab">

            <div class="row">
                <div class="col-md-12">

                    <div class="table-responsive">
                        <table class="table custom-table mb-0 datatable">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="od-check" id="checkAllPolicies">
                                    </th>
                                    <th>Policy Name</th>
                                    <th>Description</th>
                                    <th>Notify Type</th>
                                    <th>Acknowledgement</th>
                                    <th>Created</th>

                                    @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                        <th class="text-end">Actions</th>
                                    @endif
                                </tr>
                            </thead>

                            <tbody>
                                    @foreach($policies as $key => $policy)
<tr class="row-check" id="policy-row-{{ $policy->id }}">
    <td>
        <input type="checkbox" class="od-check row-checkbox">
    </td>

    <td data-label="Policy Name">{{ $policy->policy_name }}</td>

    <td data-label="Description" class="text-muted">
        {{ $policy->description }}
    </td>

    <td data-label="Notify Type">
        <span class="badge bg-info">
            {{ str_replace(',', ', ', $policy->notify_type ?? 'None') }}
        </span>
    </td>

    <td data-label="Acknowledgement">
        @if($policy->acknowledge_required)
            <span class="badge bg-warning">Required</span>
            @if($policy->deadline_date)
                <br>
                <small>
                    Deadline: {{ \Carbon\Carbon::parse($policy->deadline_date)->format('d M Y') }}
                </small>
            @endif
        @else
            <span class="badge bg-secondary">Not Required</span>
        @endif
    </td>

    <td data-label="Created">
        {{ \Carbon\Carbon::parse($policy->created_at)->format('d M Y') }}
    </td>

    <td data-label="Action" class="text-end od-inline-actions">
        <a href="{{ route('policies.download', $policy->id) }}"
           class="od-icon-btn" title="Download">
            <i class="fa-solid fa-download"></i>
        </a>

        @if(isset($permissions) && $permissions->can_edit)
        <a href="#" class="od-icon-btn"
           data-bs-toggle="modal"
           data-bs-target="#edit_policy_{{ $policy->id }}"
           title="Edit">
            <i class="fa-solid fa-pencil"></i>
        </a>
        @endif

        @if(isset($permissions) && $permissions->can_delete)
        <a href="#" class="od-icon-btn"
           data-bs-toggle="modal"
           data-bs-target="#delete_policy_{{ $policy->id }}"
           title="Delete">
            <i class="fa-regular fa-trash-can"></i>
        </a>
        @endif
    </td>
</tr>
@endforeach

</tbody>
</table>
</div>
</div>
</div>

</div> <!-- END TAB 1 -->



<!-- =====================================================
     TAB 2 : LOGS TABLE (DYNAMIC)
====================================================== -->
<div class="tab-pane fade" id="logs-tab">
    <div class="card p-3">
        <h5 class="mb-3">Policy Activity Logs</h5>

        <div class="table-responsive">
            <table class="table custom-table mb-0 datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Policy Name</th>
                        <th>Action</th>
                        <th>Employee</th>
                      
                        <th>Details</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($log->policy_name)
                                {{ $log->policy_name }}
                            @else
                                <span class="text-muted">Policy Deleted</span>
                            @endif
                        </td>
                        <td>
                            @switch($log->action)
                                @case('created')
                                    <span class="badge bg-success">Created</span>
                                    @break
                                @case('updated')
                                    <span class="badge bg-primary">Edited</span>
                                    @break
                                @case('deleted')
                                    <span class="badge bg-danger">Deleted</span>
                                    @break
                                @case('email_sent')
                                    <span class="badge bg-info">Email Sent</span>
                                    @break
                                @case('acknowledged')
                                    <span class="badge bg-warning">Acknowledged</span>
                                    @break
                                @case('downloaded')
                                    <span class="badge bg-secondary">Downloaded</span>
                                    @break
                                @default
                                    <span class="badge bg-dark">{{ ucfirst($log->action) }}</span>
                            @endswitch
                        </td>
                        <td>
                            @if($log->user_firstname)
                                {{ $log->user_firstname }} {{ $log->user_lastname }}
                            @else
                                <span class="text-muted">Admin</span>
                            @endif
                        </td>
                      
                        <td class="text-muted">
                            {{ $log->details ?? 'No details' }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($log->action_date)->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @endforeach

                    @if($logs->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No activity logs found
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </div>
</div> <!-- END TAB 2 -->

</div> <!-- END tab-content -->
</div> <!-- END content container -->
<!-- =====================================================
     ADD POLICY MODAL  (unchanged)
====================================================== -->
<div id="add_policy" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Add Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('policies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="input-block mb-3">
                        <label class="col-form-label">Policy Name <span class="text-danger">*</span></label>
                        <input name="policy_name" class="form-control @error('policy_name') is-invalid @enderror"
                               type="text" required value="{{ old('policy_name') }}">
                        @error('policy_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- FILE ACCESS -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">File Access</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="file_access" id="access_employee"
                                   value="employee" checked>
                            <label class="form-check-label" for="access_employee">Active Employee</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="file_access" id="access_role"
                                   value="role">
                            <label class="form-check-label" for="access_role">Role</label>
                        </div>
                    </div>


                    <!-- EMPLOYEE -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                id="employee_select_add" required>

                            <option value="">-- Select Employee --</option>

                            @if(!empty($employees) && count($employees) > 0)
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->firstname }} {{ $emp->lastname }} ({{ $emp->email }})
                                    </option>
                                @endforeach
                            @else
                                <option value="">No employees available</option>
                            @endif

                        </select>
                        @error('employee_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- FILE UPLOAD -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Upload Policy <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                               required accept=".pdf,.doc,.docx">
                        @error('file')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- NOTIFICATIONS -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Notify Through</label>
                        <p class="text-muted small">Notify employees when a new file is added or edited</p>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_through[]"
                                   id="notify_feeds" value="feeds"
                                   {{ in_array('feeds', old('notify_through', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_feeds">Feeds</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_through[]"
                                   id="notify_email" value="email"
                                   {{ in_array('email', old('notify_through', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="notify_email">Email</label>
                        </div>
                    </div>


                    <!-- ACKNOWLEDGEMENT -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Acknowledgement</label>
                        <p class="text-muted small">
                            When enabled, employees must manually acknowledge reading the documents.
                        </p>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deadline_type" id="no_deadline"
                                   value="no_deadline"
                                   {{ old('deadline_type', 'no_deadline') == 'no_deadline' ? 'checked' : '' }}>
                            <label class="form-check-label" for="no_deadline">No Deadline</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deadline_type"
                                   id="enforce_deadline" value="enforce_deadline"
                                   {{ old('deadline_type') == 'enforce_deadline' ? 'checked' : '' }}>
                            <label class="form-check-label" for="enforce_deadline">Enforce Mandatory Deadline</label>
                        </div>

                        <div id="deadline_date_section" class="mt-3"
                             style="display: {{ old('deadline_type') == 'enforce_deadline' ? 'block' : 'none' }};">
                            <input type="date" name="deadline_date"
                                   class="form-control @error('deadline_date') is-invalid @enderror"
                                   id="deadline_date_input" value="{{ old('deadline_date') }}">

                            <small class="text-warning d-block mt-2">
                                <strong>Warning:</strong> Employee must acknowledge on or before the last date.
                            </small>

                            @error('deadline_date')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="acknowledge_required"
                                   id="acknowledge_required" {{ old('acknowledge_required') ? 'checked' : '' }}>
                            <label class="form-check-label" for="acknowledge_required">
                                Require Acknowledgement
                            </label>
                        </div>
                    </div>


                    <div class="submit-section d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 submit-btn">Submit</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<!-- /ADD POLICY MODAL -->




<!-- =====================================================
     EDIT POLICY MODALS (unchanged)
====================================================== -->
@foreach($policies as $policy)
<div id="edit_policy_{{ $policy->id }}" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('policies.update', $policy->id) }}" method="POST"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')


                    <div class="input-block mb-3">
                        <label class="col-form-label">Policy Name <span class="text-danger">*</span></label>
                        <input name="policy_name"
                               class="form-control @error('policy_name') is-invalid @enderror"
                               type="text" value="{{ $policy->policy_name }}" required>
                        @error('policy_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="input-block mb-3">
                        <label class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4" required>{{ $policy->description }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- FILE ACCESS -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">File Access</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="file_access"
                                   value="employee"
                                   {{ $policy->file_access == 'employee' ? 'checked' : '' }}>
                            <label class="form-check-label">Active Employee</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="file_access"
                                   value="role" {{ $policy->file_access == 'role' ? 'checked' : '' }}>
                            <label class="form-check-label">Role</label>
                        </div>
                    </div>


                    <!-- EMPLOYEE -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Employee <span class="text-danger">*</span></label>

                        <select name="employee_id"
                                class="form-control @error('employee_id') is-invalid @enderror"
                                id="employee_select_edit_{{ $policy->id }}" required>

                            <option value="">-- Select Employee --</option>

                            @foreach($employees as $emp)
                            <option value="{{ $emp->id }}"
                                {{ $emp->id == $policy->employee_id ? 'selected' : '' }}>
                                {{ $emp->firstname }} {{ $emp->lastname }} ({{ $emp->email }})
                            </option>
                            @endforeach

                        </select>
                        @error('employee_id')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- CURRENT FILE -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Current File</label>

                        <div class="current-file-container">
                            @if($policy->file_path)
                                <i class="fa fa-file-text-o file-icon"></i>
                                <span class="file-name">{{ basename($policy->file_path) }}</span>

                                <a href="{{ route('policies.download', $policy->id) }}"
                                   class="download-btn">
                                    <i class="fa fa-download"></i>
                                </a>
                            @else
                                <span class="text-muted">No file uploaded</span>
                            @endif
                        </div>
                    </div>


                    <!-- NEW FILE -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Upload New File (Optional)</label>

                        <input type="file" name="file"
                               class="form-control @error('file') is-invalid @enderror"
                               accept=".pdf,.doc,.docx">

                        <small class="text-muted">Leave blank to keep the current file.</small>

                        @error('file')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- NOTIFICATIONS -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Notify Through</label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_through[]"
                                   value="feeds"
                                   {{ strpos($policy->notify_type, 'feeds') !== false ? 'checked' : '' }}>
                            <label class="form-check-label">Feeds</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="notify_through[]"
                                   value="email"
                                   {{ strpos($policy->notify_type, 'email') !== false ? 'checked' : '' }}>
                            <label class="form-check-label">Email</label>
                        </div>
                    </div>


                    <!-- ACKNOWLEDGEMENT -->
                    <div class="input-block mb-3">
                        <label class="col-form-label">Acknowledgement</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deadline_type"
                                   value="no_deadline"
                                   {{ !$policy->deadline_date ? 'checked' : '' }}>
                            <label class="form-check-label">No Deadline</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="deadline_type"
                                   value="enforce_deadline"
                                   {{ $policy->deadline_date ? 'checked' : '' }}>
                            <label class="form-check-label">Enforce Mandatory Deadline</label>
                        </div>

                        <div class="mt-3">
                            <input type="date" name="deadline_date"
                                   class="form-control @error('deadline_date') is-invalid @enderror"
                                   value="{{ $policy->deadline_date }}">
                            @error('deadline_date')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox"
                                   name="acknowledge_required"
                                   {{ $policy->acknowledge_required ? 'checked' : '' }}>
                            <label class="form-check-label">Require Acknowledgement</label>
                        </div>
                    </div>


                    <div class="submit-section">
                        <button type="button" class="btn btn-secondary me-2 submit-btn"
                                data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-primary submit-btn">Update</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endforeach
<!-- =====================================================
     DELETE POLICY MODALS (unchanged)
====================================================== -->
@foreach($policies as $policy)
<div id="delete_policy_{{ $policy->id }}" class="modal fade" role="dialog" tabindex="-1"
     aria-labelledby="deletePolicyModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p>Are you sure you want to delete this policy:
                    <strong>{{ $policy->policy_name }}</strong>?
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                <form action="{{ route('policies.destroy', $policy->id) }}" method="POST"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endforeach




<!-- =====================================================
     JAVASCRIPT  (unchanged)
====================================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Employee dropdown fix
    const employeeSelects = document.querySelectorAll('[id^="employee_select"]');
    employeeSelects.forEach(select => {
        select.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // Add Policy modal - deadline toggle
    const addPolicyForm = document.querySelector('#add_policy form');
    if (addPolicyForm) {
        addPolicyForm.querySelectorAll('input[name="deadline_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const section = this.closest('form').querySelector('#deadline_date_section');

                if (!section) return;

                if (this.value === 'enforce_deadline') {
                    section.style.display = 'block';
                    section.querySelector('input[type="date"]').required = true;
                } else {
                    section.style.display = 'none';
                    section.querySelector('input[type="date"]').required = false;
                }
            });
        });
    }

    // Edit Policy modals - deadline toggle
    document.querySelectorAll('.modal form').forEach(form => {
        if (form.getAttribute('action') && form.getAttribute('action').includes('policies')) {

            form.querySelectorAll('input[name="deadline_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const section = form.querySelector('#deadline_date_section');

                    if (!section) return;

                    if (this.value === 'enforce_deadline') {
                        section.style.display = 'block';
                        section.querySelector('input[type="date"]').required = true;
                    } else {
                        section.style.display = 'none';
                        section.querySelector('input[type="date"]').required = false;
                    }
                });
            });
        }
    });

});
</script>




<!-- =====================================================
     TABS CSS (Zoho Style)
====================================================== -->
<style>
.leave-tabs .nav-link {
    font-size: 15px;
    font-weight: 500;
    color: #333;
 
    border-bottom: 3px solid transparent;
    padding: 10px 18px;
}

.leave-tabs .nav-link.active {
    color: #f97316;
    border-bottom: 3px solid #f97316;
}

.tabs-underline {
    width: 100%;
    height: 2px;
    background: #e5eaf2;
    margin-top: -4px;
    margin-bottom: 12px;
}
</style>


@endsection
