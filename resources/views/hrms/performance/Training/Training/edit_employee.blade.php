@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 15px;">
    <!-- Page Content -->
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <!-- Page Header -->
        <div class="page-header" style="margin-bottom: 10px;">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Training Details</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('trainings.index') }}">Training</a></li>
                        <li class="breadcrumb-item active">Edit Training Details</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Training Details for {{ $employee->firstname }} {{ $employee->lastname }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- Employee Info -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="employee-info-card p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        @if($employee->profile_image)
                                            <img src="{{ asset($employee->profile_image) }}" alt="Profile Image"
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; margin-right: 15px;">
                                        @else
                                            <div style="width: 60px; height: 60px; background-color: #e9ecef; border-radius: 50%; margin-right: 15px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-user fa-2x" style="color: #6c757d;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 class="mb-1">{{ $employee->firstname }} {{ $employee->lastname }}</h5>
                                            <p class="mb-1"><strong>Employee ID:</strong> {{ $employee->employeeid }}</p>
                                            <p class="mb-0"><strong>Assigned Trainer:</strong> {{ $employee->trainer_name ?? 'Not Assigned' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Training Details Form -->
                        <form action="{{ route('trainings.updateEmployee', $employee->id) }}" method="POST" enctype="multipart/form-data" id="trainingForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Basic Training Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Basic Training Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_name">Training Name</label>
                                                <input type="text" class="form-control"
                                                        id="training_name" name="training_name"
                                                        value="{{ old('training_name', $employee->training_name) }}"
                                                        placeholder="e.g., Cybersecurity Awareness 2025">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_category">Training Category</label>
                                                <select class="form-control"
                                                        id="training_category" name="training_category">
                                                    <option value="">Select Category</option>
                                                    <option value="technical" {{ old('training_category', $employee->training_category) == 'technical' ? 'selected' : '' }}>Technical</option>
                                                    <option value="soft_skills" {{ old('training_category', $employee->training_category) == 'soft_skills' ? 'selected' : '' }}>Soft Skills</option>
                                                    <option value="compliance" {{ old('training_category', $employee->training_category) == 'compliance' ? 'selected' : '' }}>Compliance</option>
                                                    <option value="leadership" {{ old('training_category', $employee->training_category) == 'leadership' ? 'selected' : '' }}>Leadership</option>
                                                    <option value="safety" {{ old('training_category', $employee->training_category) == 'safety' ? 'selected' : '' }}>Safety</option>
                                                    <option value="other" {{ old('training_category', $employee->training_category) == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="trainer_name">Trainer Name</label>
                                                <input type="text" class="form-control"
                                                        id="trainer_name" name="trainer_name"
                                                        value="{{ old('trainer_name', $employee->trainer_name) }}"
                                                        placeholder="Select or enter trainer name">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="trainer_type">Trainer Type</label>
                                                <select class="form-control"
                                                        id="trainer_type" name="trainer_type">
                                                    <option value="">Select Type</option>
                                                    <option value="internal" {{ old('trainer_type', $employee->trainer_type) == 'internal' ? 'selected' : '' }}>Internal</option>
                                                    <option value="external" {{ old('trainer_type', $employee->trainer_type) == 'external' ? 'selected' : '' }}>External</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_mode">Training Mode</label>
                                                <select class="form-control"
                                                        id="training_mode" name="training_mode">
                                                    <option value="">Select Mode</option>
                                                    <option value="online" {{ old('training_mode', $employee->training_mode) == 'online' ? 'selected' : '' }}>Online</option>
                                                    <option value="offline" {{ old('training_mode', $employee->training_mode) == 'offline' ? 'selected' : '' }}>Offline</option>
                                                    <option value="hybrid" {{ old('training_mode', $employee->training_mode) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_location">Training Location / Platform</label>
                                                <input type="text" class="form-control"
                                                        id="training_location" name="training_location"
                                                        value="{{ old('training_location', $employee->training_location) }}"
                                                        placeholder="e.g., Conference Room A, Zoom, Google Meet">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_cost">Training Cost ({{ config('app.currency', 'USD') }})</label>
                                                <input type="number" class="form-control"
                                                        id="training_cost" name="training_cost"
                                                        value="{{ old('training_cost', $employee->training_cost) }}"
                                                        min="0" step="0.01" placeholder="0.00">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="max_participants">Max Participants</label>
                                                <input type="number" class="form-control"
                                                        id="max_participants" name="max_participants"
                                                        value="{{ old('max_participants', $employee->max_participants) }}"
                                                        min="1" placeholder="e.g., 30">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_status">Training Status</label>
                                                <select class="form-control"
                                                        id="training_status" name="training_status">
                                                    <option value="planned" {{ old('training_status', $employee->training_status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                                    <option value="ongoing" {{ old('training_status', $employee->training_status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                                    <option value="completed" {{ old('training_status', $employee->training_status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ old('training_status', $employee->training_status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <div class="form-check mt-4">
                                                    <input type="checkbox" class="form-check-input"
                                                            id="certification_required" name="certification_required" value="1"
                                                            {{ old('certification_required', $employee->certification_required) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="certification_required">Certification Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="department_eligibility">Department / Role Eligibility</label>
                                                <select class="form-control select2-multiple"
                                                        id="department_eligibility" name="department_eligibility[]" multiple
                                                        data-placeholder="Select eligible departments/roles">
                                                    <!-- Options will be populated dynamically -->
                                                    <option value="it" {{ in_array('it', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>IT Department</option>
                                                    <option value="hr" {{ in_array('hr', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>HR Department</option>
                                                    <option value="finance" {{ in_array('finance', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Finance Department</option>
                                                    <option value="sales" {{ in_array('sales', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Sales Department</option>
                                                    <option value="marketing" {{ in_array('marketing', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Marketing Department</option>
                                                    <option value="operations" {{ in_array('operations', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Operations Department</option>
                                                    <option value="manager" {{ in_array('manager', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Managers</option>
                                                    <option value="supervisor" {{ in_array('supervisor', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>Supervisors</option>
                                                    <option value="employee" {{ in_array('employee', old('department_eligibility', explode(',', $employee->department_eligibility))) ? 'selected' : '' }}>All Employees</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Existing Training Schedule Section -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Training Schedule</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="start_date">Start Date</label>
                                                <input type="date" class="form-control"
                                                        id="start_date" name="start_date"
                                                        value="{{ old('start_date', $employee->start_date) }}">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="end_date">End Date</label>
                                                <input type="date" class="form-control"
                                                        id="end_date" name="end_date"
                                                        value="{{ old('end_date', $employee->end_date) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="training_type">Training Type (Specific)</label>
                                                <input type="text" class="form-control"
                                                        id="training_type" name="training_type"
                                                        value="{{ old('training_type', $employee->training_type) }}"
                                                        placeholder="e.g., Technical Training, Soft Skills, etc.">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="duration_hours">Duration (Hours)</label>
                                                <input type="number" class="form-control"
                                                        id="duration_hours" name="duration_hours"
                                                        value="{{ old('duration_hours', $employee->duration_hours) }}"
                                                        min="0" step="0.5" placeholder="e.g., 40">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="description">Training Description</label>
                                                <textarea class="form-control"
                                                           id="description" name="description" rows="4"
                                                           placeholder="Enter training description, objectives, and any specific requirements...">{{ old('description', $employee->training_description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Training Resources Section (Existing - Keep as is) -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Training Resources</h5>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addResource()">
                                        <i class="fa fa-plus"></i> Add Resource
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div id="training-resources">
                                        <!-- Existing resources code remains the same -->
                                        @if(isset($trainingResources) && count($trainingResources) > 0)
                                            @foreach($trainingResources as $index => $resource)
                                                <div class="resource-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Resource {{ $index + 1 }}</h6>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="removeResource(this)">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label>Resource Title</label>
                                                                <input type="text" class="form-control" name="resource_titles[]" 
                                                                       value="{{ is_array($resource) ? $resource['title'] : $resource->title }}" 
                                                                       placeholder="Enter resource title">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label>Resource Type</label>
                                                                <select class="form-control" name="resource_types[]">
                                                                    @php $resourceType = is_array($resource) ? $resource['resource_type'] : $resource->resource_type; @endphp
                                                                    <option value="module" {{ $resourceType == 'module' ? 'selected' : '' }}>Course Module</option>
                                                                    <option value="video" {{ $resourceType == 'video' ? 'selected' : '' }}>Video</option>
                                                                    <option value="link" {{ $resourceType == 'link' ? 'selected' : '' }}>External Link</option>
                                                                    <option value="document" {{ $resourceType == 'document' ? 'selected' : '' }}>Document</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group mb-3">
                                                                <label>Order</label>
                                                                <input type="number" class="form-control" name="order_sequence[]" 
                                                                       value="{{ is_array($resource) ? $resource['order_sequence'] : $resource->order_sequence }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group mb-3">
                                                                <label>Description</label>
                                                                <textarea class="form-control" name="resource_descriptions[]" rows="2" 
                                                                          placeholder="Enter resource description">{{ is_array($resource) ? $resource['description'] : $resource->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label>External URL (for videos/links)</label>
                                                                <input type="url" class="form-control" name="resource_urls[]" 
                                                                       value="{{ is_array($resource) ? $resource['external_url'] : $resource->external_url }}" 
                                                                       placeholder="https://example.com">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-3">
                                                                <label>Upload File (PDF, DOC, Video)</label>
                                                                <input type="file" class="form-control" name="resource_files[]" 
                                                                       accept=".pdf,.doc,.docx,.mp4,.avi,.mov,.wmv">
                                                                @php $filePath = is_array($resource) ? $resource['file_path'] : $resource->file_path; @endphp
                                                                @if($filePath)
                                                                    <small class="text-muted">Current file: {{ basename($filePath) }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-check">
                                                                @php $isMandatory = is_array($resource) ? $resource['is_mandatory'] : $resource->is_mandatory; @endphp
                                                                <input type="checkbox" class="form-check-input" name="is_mandatory[{{ $index }}]" 
                                                                       value="1" {{ $isMandatory ? 'checked' : '' }}>
                                                                <label class="form-check-label">Mandatory Resource</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Default empty resource form -->
                                            <div class="resource-item border rounded p-3 mb-3" data-index="0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">Resource 1</h6>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeResource(this)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label>Resource Title</label>
                                                            <input type="text" class="form-control" name="resource_titles[]" 
                                                                   placeholder="Enter resource title">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-3">
                                                            <label>Resource Type</label>
                                                            <select class="form-control" name="resource_types[]">
                                                                <option value="module">Course Module</option>
                                                                <option value="video">Video</option>
                                                                <option value="link">External Link</option>
                                                                <option value="document">Document</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mb-3">
                                                            <label>Order</label>
                                                            <input type="number" class="form-control" name="order_sequence[]" 
                                                                   value="0" min="0">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group mb-3">
                                                            <label>Description</label>
                                                            <textarea class="form-control" name="resource_descriptions[]" rows="2" 
                                                                      placeholder="Enter resource description"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label>External URL (for videos/links)</label>
                                                            <input type="url" class="form-control" name="resource_urls[]" 
                                                                   placeholder="https://example.com">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mb-3">
                                                            <label>Upload File (PDF, DOC, Video)</label>
                                                            <input type="file" class="form-control" name="resource_files[]" 
                                                                   accept=".pdf,.doc,.docx,.mp4,.avi,.mov,.wmv">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input" name="is_mandatory[0]" value="1">
                                                            <label class="form-check-label">Mandatory Resource</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-end">
                                <a href="{{ route('trainings.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Training Details</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Content -->
</div>
<!-- /Page Wrapper -->

<script>
    let resourceIndex = {{ isset($trainingResources) ? count($trainingResources) : 1 }};

    // Ensure end date is not before start date
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        const endDateInput = document.getElementById('end_date');
        endDateInput.min = startDate;
        
        if (endDateInput.value && endDateInput.value < startDate) {
            endDateInput.value = startDate;
        }
    });

    // Initialize Select2 for multi-select
    $(document).ready(function() {
        $('#department_eligibility').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    });

    function addResource() {
        const resourcesContainer = document.getElementById('training-resources');
        const resourceHtml = `
            <div class="resource-item border rounded p-3 mb-3" data-index="${resourceIndex}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Resource ${resourceIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeResource(this)">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Resource Title</label>
                            <input type="text" class="form-control" name="resource_titles[]" 
                                   placeholder="Enter resource title">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Resource Type</label>
                            <select class="form-control" name="resource_types[]">
                                <option value="module">Course Module</option>
                                <option value="video">Video</option>
                                <option value="link">External Link</option>
                                <option value="document">Document</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-3">
                            <label>Order</label>
                            <input type="number" class="form-control" name="order_sequence[]" 
                                   value="${resourceIndex}" min="0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Description</label>
                            <textarea class="form-control" name="resource_descriptions[]" rows="2" 
                                      placeholder="Enter resource description"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>External URL (for videos/links)</label>
                            <input type="url" class="form-control" name="resource_urls[]" 
                                   placeholder="https://example.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Upload File (PDF, DOC, Video)</label>
                            <input type="file" class="form-control" name="resource_files[]" 
                                   accept=".pdf,.doc,.docx,.mp4,.avi,.mov,.wmv">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_mandatory[${resourceIndex}]" value="1">
                            <label class="form-check-label">Mandatory Resource</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        resourcesContainer.insertAdjacentHTML('beforeend', resourceHtml);
        resourceIndex++;
    }

    function removeResource(button) {
        const resourceItem = button.closest('.resource-item');
        resourceItem.remove();
    }

    // Debug form submission
    document.getElementById('trainingForm').addEventListener('submit', function(e) {
        console.log('Form being submitted...');
        
        // Log all form data
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
    });
</script>

<style>
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
</style>

@endsection