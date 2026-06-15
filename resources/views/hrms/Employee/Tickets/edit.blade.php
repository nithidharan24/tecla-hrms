@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Ticket</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}" class="breadcrumb-link">Ticket List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                        <i class="fas fa-edit"></i> Edit Ticket - {{ $ticket->ticket_id }}
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

                        <!-- Employee Edit Info -->
                        @if($role === 'employee')
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Employee Edit Mode:</strong> You can edit the ticket title, description, upload files, and change state. 
                            Category, priority, and assignments can only be changed by administrators/support team.
                        </div>
                        @endif

                        <form method="POST" action="{{ route('tickets.update', $ticket->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Title -->
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $ticket->title) }}"
                                           placeholder="Enter ticket title"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Category (Admin/Support only) -->
                                @if(in_array($role, ['admin', 'support']))
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        <option value="Hardware" {{ old('category', $ticket->category) == 'Hardware' ? 'selected' : '' }}>Hardware</option>
                                        <option value="Software" {{ old('category', $ticket->category) == 'Software' ? 'selected' : '' }}>Software</option>
                                        <option value="Network" {{ old('category', $ticket->category) == 'Network' ? 'selected' : '' }}>Network</option>
                                        <option value="HR Access" {{ old('category', $ticket->category) == 'HR Access' ? 'selected' : '' }}>HR Access</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @else
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <div>
                                        <span class="badge bg-info fs-6">{{ $ticket->category }}</span>
                                        <small class="text-muted ms-2">Category can only be changed by administrators</small>
                                    </div>
                                </div>
                                @endif

                                <!-- Priority (Admin/Support only) -->
                                @if(in_array($role, ['admin', 'support']))
                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority" 
                                            required>
                                        <option value="">Select Priority</option>
                                        <option value="Low" {{ old('priority', $ticket->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ old('priority', $ticket->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ old('priority', $ticket->priority) == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Critical" {{ old('priority', $ticket->priority) == 'Critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @else
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Priority</label>
                                    <div>
                                        @switch($ticket->priority)
                                            @case('Low')
                                                <span class="badge bg-success fs-6">Low</span>
                                                @break
                                            @case('Medium')
                                                <span class="badge bg-info fs-6">Medium</span>
                                                @break
                                            @case('High')
                                                <span class="badge bg-warning fs-6">High</span>
                                                @break
                                            @case('Critical')
                                                <span class="badge bg-danger fs-6">Critical</span>
                                                @break
                                        @endswitch
                                        <small class="text-muted ms-2">Priority can only be changed by administrators</small>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <!-- States (All users can update) -->
                                <div class="col-md-6 mb-3">
                                    <label for="states" class="form-label">State <span class="text-danger">*</span></label>
                                    <select class="form-control @error('states') is-invalid @enderror" 
                                            id="states" 
                                            name="states" 
                                            required>
                                        <option value="">Select State</option>
                                        <option value="Open" {{ old('states', $ticket->states) == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="In progress" {{ old('states', $ticket->states) == 'In progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Waiting" {{ old('states', $ticket->states) == 'Waiting' ? 'selected' : '' }}>Waiting</option>
                                        <option value="Resolved" {{ old('states', $ticket->states) == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                        <option value="Closed" {{ old('states', $ticket->states) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @error('states')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($role === 'employee')
                                        <small class="text-muted">You can update the state to track your ticket progress</small>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <!-- Raised By (Employee dropdown) - Admin/Support only -->
                                @if(in_array($role, ['admin', 'support']))
                                <div class="col-md-6 mb-3">
                                    <label for="raised_by" class="form-label">Raised By <span class="text-danger">*</span></label>
                                    <select class="form-control @error('raised_by') is-invalid @enderror" 
                                            id="raised_by" 
                                            name="raised_by" 
                                            required>
                                        <option value="">Select Employee</option>
                                        @foreach($allEmployees as $employee)
                                            <option value="{{ $employee->id }}" 
                                                {{ old('raised_by', $ticket->raised_by) == $employee->id ? 'selected' : '' }}
                                                data-department="{{ $employee->department }}">
                                                {{ $employee->firstname }} {{ $employee->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('raised_by')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @else
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Raised By</label>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $raiser = $allEmployees->firstWhere('id', $ticket->raised_by);
                                        @endphp
                                        @if($raiser)
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px;">
                                                {{ strtoupper(substr($raiser->firstname, 0, 1)) }}
                                            </div>
                                            <span>{{ $raiser->firstname }} {{ $raiser->lastname }}</span>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Assigned To (Support Team) - Admin/Support only -->
                                @if(in_array($role, ['admin', 'support']))
                                <div class="col-md-6 mb-3">
                                    <label for="assigned_to" class="form-label">Assigned To (Support Team)</label>
                                    <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                            id="assigned_to" 
                                            name="assigned_to">
                                        <option value="">Select Support Staff</option>
                                        @foreach($supportTeam as $support)
                                            <option value="{{ $support->id }}" {{ old('assigned_to', $ticket->assigned_to) == $support->id ? 'selected' : '' }}>
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
                                @else
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assigned To</label>
                                    <div class="d-flex align-items-center">
                                        @if($ticket->assigned_to)
                                            @php
                                                $assignee = $supportTeam->firstWhere('id', $ticket->assigned_to);
                                            @endphp
                                            @if($assignee)
                                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px;">
                                                    {{ strtoupper(substr($assignee->firstname, 0, 1)) }}
                                                </div>
                                                <span>{{ $assignee->firstname }} {{ $assignee->lastname }} ({{ $assignee->designation_name }})</span>
                                            @else
                                                <span class="text-muted">Not Assigned</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <!-- Department (Auto-filled) -->
                                <div class="col-md-6 mb-3">
                                    <label for="dept_display" class="form-label">Department</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="dept_display" 
                                           readonly
                                           value="{{ $ticket->dept ?? '' }}"
                                           placeholder="Department auto-fills based on employee">
                                    <small class="text-muted">Department is automatically filled based on selected employee</small>
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
                                              required>{{ old('description', $ticket->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Current File Display -->
                                @if($ticket->uploaded_files)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Current Attachment</label>
                                    <div class="current-file">
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <i class="fas fa-file me-2"></i>
                                            <a href="{{ route('tickets.download', $ticket->id) }}" target="_blank" class="text-decoration-none">
                                                {{ basename($ticket->uploaded_files) }}
                                            </a>
                                            <span class="ms-2 text-muted">
                                                ({{ round(Storage::disk('public')->size($ticket->uploaded_files) / 1024, 2) }} KB)
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Upload New Files -->
                                <div class="col-md-6 mb-3">
                                    <label for="uploaded_files" class="form-label">
                                        {{ $ticket->uploaded_files ? 'Replace Attachment' : 'Upload Attachment' }}
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('uploaded_files') is-invalid @enderror" 
                                           id="uploaded_files" 
                                           name="uploaded_files"
                                           accept=".jpg,.jpeg,.png,.pdf,.docx,.doc,.xls,.xlsx,.txt">
                                    <small class="text-muted">
                                        Allowed: jpg, jpeg, png, pdf, docx, doc, xls, xlsx, txt (Max: 10MB)
                                        @if($ticket->uploaded_files)
                                            <br><strong>Note:</strong> Uploading a new file will replace the current attachment.
                                        @endif
                                    </small>
                                    @error('uploaded_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ticket Info Card -->
                            <div class="row mt-3">
                                <div class="col-md-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Ticket Information</h6>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <strong>Ticket ID:</strong><br>
                                                    <span class="text-muted">{{ $ticket->ticket_id }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Created:</strong><br>
                                                    <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, h:i A') }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Last Updated:</strong><br>
                                                    <span class="text-muted">{{ \Carbon\Carbon::parse($ticket->updated_at)->format('d M Y, h:i A') }}</span>
                                                </div>
                                                <div class="col-md-3">
                                                    <strong>Current State:</strong><br>
                                                    @switch($ticket->states)
                                                        @case('Open')
                                                            <span class="badge bg-primary">Open</span>
                                                            @break
                                                        @case('In progress')
                                                            <span class="badge bg-warning">In Progress</span>
                                                            @break
                                                        @case('Waiting')
                                                            <span class="badge bg-secondary">Waiting</span>
                                                            @break
                                                        @case('Resolved')
                                                            <span class="badge bg-info">Resolved</span>
                                                            @break
                                                        @case('Closed')
                                                            <span class="badge bg-success">Closed</span>
                                                            @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Ticket
                                    </button>
                                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
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
        // Store employee data with department IDs
        const employeeDepartments = @json($allEmployees->mapWithKeys(function($employee) {
            return [$employee->id => $employee->department];
        }));

        function updateDepartment() {
            const employeeId = $('#raised_by').val();
            if (employeeId && employeeDepartments[employeeId]) {
                const deptId = employeeDepartments[employeeId];
                $('#dept_display').val('Loading...');
                
                // AJAX call to get department name
                $.ajax({
                    url: '/api/department/' + deptId,
                    method: 'GET',
                    success: function(data) {
                        $('#dept_display').val(data.department);
                    },
                    error: function() {
                        $('#dept_display').val('Department ID: ' + deptId);
                    }
                });
            } else {
                $('#dept_display').val('');
            }
        }

        // Update department when employee selection changes (only for admin/support)
        @if(in_array($role, ['admin', 'support']))
        $('#raised_by').on('change', updateDepartment);
        @endif

        // Form validation
        $('form').on('submit', function(e) {
            const title = $('#title').val().trim();
            const description = $('#description').val().trim();
            const states = $('#states').val();
            
            if (!title) {
                e.preventDefault();
                alert('Please enter a title.');
                return false;
            }
            
            if (!description) {
                e.preventDefault();
                alert('Please enter a description.');
                return false;
            }
            
            if (!states) {
                e.preventDefault();
                alert('Please select a state.');
                return false;
            }
            
            @if(in_array($role, ['admin', 'support']))
            const category = $('#category').val();
            const priority = $('#priority').val();
            const raised_by = $('#raised_by').val();
            
            if (!category) {
                e.preventDefault();
                alert('Please select a category.');
                return false;
            }
            
            if (!priority) {
                e.preventDefault();
                alert('Please select a priority.');
                return false;
            }
            
            if (!raised_by) {
                e.preventDefault();
                alert('Please select who raised the ticket.');
                return false;
            }
            @endif
        });
    });
</script>
@endpush
@endsection