@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Create New Ticket</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}" class="breadcrumb-link">Tickets</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h5 class="card-header bg-primary text-white">
                        <i class="fas fa-plus-circle"></i> New Ticket
                    </h5>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title') }}"
                                           placeholder="Enter ticket title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        <option value="Hardware" {{ old('category') == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                        <option value="Software" {{ old('category') == 'Software' ? 'selected' : '' }}>Software</option>
                                        <option value="Network" {{ old('category') == 'Network' ? 'selected' : '' }}>Network</option>
                                        <option value="HR Access" {{ old('category') == 'HR Access' ? 'selected' : '' }}>HR Access</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Priority -->
<div class="col-md-6 mb-3">
    <label for="priority" class="form-label">
        Priority <span class="text-danger">*</span>
    </label>

    <select class="form-control @error('priority') is-invalid @enderror"
            id="priority"
            name="priority"
            required>

        <option value="">Select Priority</option>

        <!-- Standard -->
        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
        <option value="Critical" {{ old('priority') == 'Critical' ? 'selected' : '' }}>Critical</option>

        <!-- CSPM Category -->
        <option value="CSPM_Critical" {{ old('priority') == 'CSPM_Critical' ? 'selected' : '' }}>
            CSPM - Critical
        </option>
        <option value="CSPM_High" {{ old('priority') == 'CSPM_High' ? 'selected' : '' }}>
            CSPM - High
        </option>

        <!-- MA Category -->
        <option value="MA_Critical" {{ old('priority') == 'MA_Critical' ? 'selected' : '' }}>
            MA - Critical
        </option>
        <option value="MA_High" {{ old('priority') == 'MA_High' ? 'selected' : '' }}>
            MA - High
        </option>

    </select>

    @error('priority')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                            </div>

                            <div class="row">
                                <!-- Raised By (Employee dropdown) -->
                                <div class="col-md-6 mb-3">
                                    <label for="raised_by" class="form-label">Raised By <span class="text-danger">*</span></label>
                                    <select class="form-control @error('raised_by') is-invalid @enderror" 
                                            id="raised_by" 
                                            name="raised_by" 
                                            required>
                                        <option value="">Select Employee</option>
                                        @foreach($allEmployees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ old('raised_by', $role === 'employee' ? $employeeId : '') == $employee->id ? 'selected' : '' }}
                                                data-department="{{ $employee->department }}">
                                                {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('raised_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Assigned To (Support Team) -->
                                <div class="col-md-6 mb-3">
                                    <label for="assigned_to" class="form-label">Assigned To (Support Team)</label>
                                    <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                            id="assigned_to" 
                                            name="assigned_to">
                                        <option value="">Select Support Staff</option>
                                        @foreach($supportTeam as $support)
                                            <option value="{{ $support->id }}" {{ old('assigned_to') == $support->id ? 'selected' : '' }}>
                                                {{ $support->firstname }} {{ $support->lastname }} 
                                                ({{ $support->designation_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Only support team members are shown here</small>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Department (Auto-filled) -->
                                <div class="col-md-6 mb-3">
                                    <label for="dept_display" class="form-label">Department</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dept_display" 
                                           readonly
                                           placeholder="Department will auto-fill">
                                    <small class="text-muted">Department is automatically filled based on selected employee</small>
                                </div>

                                <!-- Attachment -->
                                <div class="col-md-6 mb-3">
                                    <label for="uploaded_files" class="form-label">Attachment</label>
                                    <input type="file" 
                                           class="form-control @error('uploaded_files') is-invalid @enderror" 
                                           id="uploaded_files" 
                                           name="uploaded_files"
                                           accept=".jpg,.jpeg,.png,.pdf,.docx,.doc,.xls,.xlsx,.txt">
                                    <small class="text-muted">Allowed: jpg, jpeg, png, pdf, docx, doc, xls, xlsx, txt (Max: 10MB)</small>
                                    @error('uploaded_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="6"
                                              placeholder="Provide detailed description of the issue..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Submit Ticket
                                    </button>
                                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Department mapping (you may need to adjust this based on your database)
        // This is a simplified version - in production, you might want to fetch via AJAX
        const departments = @json($allEmployees->mapWithKeys(function($employee) {
            return [$employee->id => $employee->department];
        }));

        // Get department name from department ID (you'll need to enhance this)
        const departmentNames = {
            // Add mapping based on your department table
            // Example: 1: 'IT', 2: 'HR', etc.
        };

        function updateDepartment() {
            const employeeId = $('#raised_by').val();
            if (employeeId && departments[employeeId]) {
                const deptId = departments[employeeId];
                // You might want to get the department name via AJAX
                $('#dept_display').val('Loading...');
                
                // AJAX call to get department name
                $.get('/api/department/' + deptId, function(data) {
                    $('#dept_display').val(data.name);
                }).fail(function() {
                    $('#dept_display').val('Department ID: ' + deptId);
                });
            } else {
                $('#dept_display').val('');
            }
        }

        // Update department when employee selection changes
        $('#raised_by').on('change', updateDepartment);
        
        // Trigger on page load if there's a selected value
        if ($('#raised_by').val()) {
            updateDepartment();
        }
    });
</script>
@endpush
@endsection