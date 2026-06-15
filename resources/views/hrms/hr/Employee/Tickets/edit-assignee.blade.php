@extends('layouts.index')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                       
                        {{ $assignee ? 'Edit Assignee' : 'Assign Employee' }}
                    </h3>
                  
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ $assignee ? route('ticket-assignees.update',$assignee->id) : route('ticket-assignees.store') }}" id="assigneeForm">
                        @csrf
                        @if($assignee) @method('PUT') @endif

                        @if(!$assignee)
                        <div class="form-group">
                            <label for="department_id">Department *</label>
                            <select name="department_id" class="form-control select2" id="department_id" required>
                                <option value="">Select Department First</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">
                                        {{ $dept->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="employee_id">Employee *</label>
                            <select name="employee_id" class="form-control select2" id="employee_id" required disabled>
                                <option value="">Select Department First</option>
                            </select>
                            <div id="employee_loading" class="d-none text-center py-2">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span class="ml-2">Loading employees...</span>
                            </div>
                            <div id="no_employees" class="d-none alert alert-warning mt-2">
                                No available employees found in this department or all employees are already assigned.
                            </div>
                        </div>
                        @else
                        <div class="form-group">
                            <label>Employee Information</label>
                            <div class="border p-3 bg-light rounded">
                                <strong>{{ $assignee->firstname }} {{ $assignee->lastname }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-building"></i> Department: {{ $assignee->department_name ?? 'N/A' }}<br>
                                    <i class="fas fa-briefcase"></i> Designation: {{ $assignee->designation_name ?? 'N/A' }}<br>
                                    <i class="fas fa-id-card"></i> Employee ID: {{ $assignee->employee_id }}
                                </small>
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="role">Role (Optional)</label>
                            <input type="text" name="role" class="form-control" id="role" 
                                   value="{{ old('role', $assignee->role ?? '') }}" 
                                   placeholder="e.g., Technical Support, First Line Support, Escalation Manager">
                            <small class="text-muted">Specific role for ticket assignments</small>
                        </div>

                        <div class="form-group">
                            <label for="priority">Priority *</label>
                            <input type="number" name="priority" min="1" max="100" class="form-control" id="priority" 
                                   value="{{ old('priority', $assignee->priority ?? 1) }}" required>
                            <small class="text-muted">Lower number = higher priority (1 is highest)</small>
                        </div>

                        <div class="form-group">
                            <label for="is_active">Status *</label>
                            <select name="is_active" class="form-control" id="is_active" required>
                                <option value="1" {{ (old('is_active', $assignee->is_active ?? 1) == 1) ? 'selected' : '' }}>
                                    Active - Can receive ticket assignments
                                </option>
                                <option value="0" {{ (old('is_active', $assignee->is_active ?? 1) == 0) ? 'selected' : '' }}>
                                    Inactive - Will not receive new tickets
                                </option>
                            </select>
                        </div>

                        <div class="form-group text-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas {{ $assignee ? 'fa-save' : 'fa-user-check' }}"></i> 
                                {{ $assignee ? 'Update Assignee' : 'Assign Employee' }}
                            </button>
                            <a href="{{ route('ticket-assignees.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
}
.bg-light {
    background-color: #f8f9fa !important;
}
#employee_loading, #no_employees {
    transition: all 0.3s ease;
}
</style>

<script>
$(document).ready(function(){
    // Initialize Select2 if available
    if($.fn.select2) {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    }

    @if(!$assignee)
    // Department change event
    $('#department_id').on('change', function(){
        var departmentId = $(this).val();
        var employeeSelect = $('#employee_id');
        var loadingDiv = $('#employee_loading');
        var noEmployeesDiv = $('#no_employees');

        if(departmentId) {
            // Show loading, hide employee select
            employeeSelect.prop('disabled', true).html('<option value="">Loading...</option>');
            loadingDiv.removeClass('d-none');
            noEmployeesDiv.addClass('d-none');

            // AJAX request to get employees
            $.ajax({
                url: '{{ route("ticket-assignees.get-employees", "") }}/' + departmentId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    loadingDiv.addClass('d-none');
                    
                    if(data.length > 0) {
                        employeeSelect.html('<option value="">Select Employee</option>');
                        $.each(data, function(key, employee) {
                            employeeSelect.append(
                                '<option value="' + employee.id + '">' + 
                                employee.firstname + ' ' + employee.lastname + 
                                ' - ' + (employee.designation_name || 'N/A') +
                                '</option>'
                            );
                        });
                        employeeSelect.prop('disabled', false);
                        noEmployeesDiv.addClass('d-none');
                    } else {
                        employeeSelect.html('<option value="">No employees available</option>');
                        employeeSelect.prop('disabled', true);
                        noEmployeesDiv.removeClass('d-none');
                    }
                    
                    // Re-initialize Select2
                    if($.fn.select2) {
                        employeeSelect.select2('destroy').select2({
                            placeholder: "Select an employee",
                            allowClear: true
                        });
                    }
                },
                error: function() {
                    loadingDiv.addClass('d-none');
                    employeeSelect.html('<option value="">Error loading employees</option>');
                    employeeSelect.prop('disabled', true);
                    alert('Error loading employees. Please try again.');
                }
            });
        } else {
            employeeSelect.html('<option value="">Select Department First</option>');
            employeeSelect.prop('disabled', true);
            loadingDiv.addClass('d-none');
            noEmployeesDiv.addClass('d-none');
        }
    });
    @endif

    // Form validation
    $('#assigneeForm').on('submit', function(e){
        let priority = parseInt($('#priority').val());
        if(priority < 1) {
            alert('Priority must be at least 1');
            e.preventDefault();
            return false;
        }
        
        if(priority > 100) {
            alert('Priority cannot exceed 100');
            e.preventDefault();
            return false;
        }

        @if(!$assignee)
        if(!$('#employee_id').val() || $('#employee_id').prop('disabled')) {
            alert('Please select a department and then select an employee');
            e.preventDefault();
            return false;
        }
        @endif
    });

    // Real-time priority validation
    $('#priority').on('change input', function(){
        let val = parseInt($(this).val());
        if(val < 1 || val > 100) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
@endsection