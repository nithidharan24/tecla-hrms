@extends('layouts.index')

@section('content')


<div class="row container mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Promotion Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('promotion.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->employeeid }}" 
                                        {{ old('employee_id', $promotion->employee_id) == $employee->employeeid ? 'selected' : '' }}>
                                        {{ $employee->firstname }} {{ $employee->lastname }} ({{ $employee->employeeid }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" 
                                        {{ old('department_id', $promotion->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="promotion_from" class="form-label">Promotion From (Current Designation) <span class="text-danger">*</span></label>
                            <select class="form-select @error('promotion_from') is-invalid @enderror" id="promotion_from" name="promotion_from" required>
                                <option value="">Select Current Designation</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}" 
                                        {{ old('promotion_from', $promotion->promotion_from) == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->designation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('promotion_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="promotion_to" class="form-label">Promotion To (New Designation) <span class="text-danger">*</span></label>
                            <select class="form-select @error('promotion_to') is-invalid @enderror" id="promotion_to" name="promotion_to" required>
                                <option value="">Select New Designation</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}" 
                                        {{ old('promotion_to', $promotion->promotion_to) == $designation->id ? 'selected' : '' }}>
                                        {{ $designation->designation }}
                                    </option>
                                @endforeach
                            </select>
                            @error('promotion_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="promotion_date" class="form-label">Promotion Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('promotion_date') is-invalid @enderror" 
                                id="promotion_date" name="promotion_date" 
                                value="{{ old('promotion_date', $promotion->promotion_date) }}" required>
                            @error('promotion_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-md">Update Promotion</button>
                        <a href="{{ route('promotion.index') }}" class="btn btn-secondary w-md ms-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        // When employee is selected, update the department and current designation
        $('#employee_id').change(function() {
            var employeeId = $(this).val();
            if(employeeId) {
                $.ajax({
                    url: '{{ route("promotion.getEmployeeDetails") }}',
                    type: 'GET',
                    data: {
                        'employee_id': employeeId
                    },
                    success: function(data) {
                        if(data.success) {
                            $('#department_id').val(data.department_id);
                            $('#promotion_from').val(data.current_designation_id);
                        }
                    }
                });
            }
        });

        // When department changes, update the designations dropdown
        $('#department_id').change(function() {
            var departmentId = $(this).val();
            if(departmentId) {
                $.ajax({
                    url: '{{ url("promotion/getDesignationsByDepartment") }}/' + departmentId,
                    type: 'GET',
                    success: function(data) {
                        $('#promotion_to').empty();
                        $('#promotion_to').append('<option value="">Select New Designation</option>');
                        $.each(data, function(key, value) {
                            $('#promotion_to').append('<option value="'+ value.id +'">'+ value.designation +'</option>');
                        });
                    }
                });
            } else {
                $('#promotion_to').empty();
                $('#promotion_to').append('<option value="">Select New Designation</option>');
            }
        });
    });
</script>
@endsection