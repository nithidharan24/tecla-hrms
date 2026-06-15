@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Testing Ticket</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('testing.index') }}" class="breadcrumb-link">Tickets</a></li>
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
                    <h4 class="card-header">Edit Bug Details</h4>
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

                        <!-- Updated form to match create page design - single bug editing -->
                        <form id="ticketForm" method="POST" action="{{ route('testing.update', $ticket->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Project Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="project_id">Project <span class="text-danger">*</span></label>
                                    <select id="project_id" name="project_id" class="form-control">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id', $ticket->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->projectname }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Priority -->
                                <div class="col-md-6 mb-3">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select id="priority" name="priority" class="form-control">
                                        <option value="">Select Priority</option>
                                        <option value="High" {{ old('priority', $ticket->priority) === 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Medium" {{ old('priority', $ticket->priority) === 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="Low" {{ old('priority', $ticket->priority) === 'Low' ? 'selected' : '' }}>Low</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Assign To -->
                                <div class="col-md-12 mb-3">
                                    <label for="assigned_to">Assign To</label>
                                    <select id="assigned_to" name="assigned_to" class="form-control">
                                        <option value="">Select Team Member (Optional)</option>
                                        @if($ticket->assigned_to)
                                            @php
                                                $assignedEmployee = $employees->firstWhere('id', $ticket->assigned_to);
                                            @endphp
                                            @if($assignedEmployee)
                                                <option value="{{ $assignedEmployee->id }}" selected>
                                                    {{ $assignedEmployee->firstname }} {{ $assignedEmployee->lastname }}
                                                </option>
                                            @endif
                                        @endif
                                    </select>
                                    <div id="assigneePreview" class="mt-2"></div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Please select a project first to see available team members.
                                    </small>
                                </div>
                            </div>

                            <!-- Single Bug Entry - matching create page design -->
                            <div class="bug-entry card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Bug Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Module Name -->
                                        <div class="col-md-12 mb-3">
                                            <label for="module_name">Module Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control module_name" 
                                                   id="module_name" name="module_name" 
                                                   value="{{ old('module_name', $ticket->module_name) }}" 
                                                   placeholder="Enter module name">
                                            <small class="form-text text-muted">Specify the module where the bug was found</small>
                                        </div>

                                        <!-- Steps to Reproduce -->
                                        <div class="col-md-12 mb-3">
                                            <label for="steps_to_reproduce">Steps to Reproduce</label>
                                            <textarea class="form-control steps_to_reproduce" 
                                                      id="steps_to_reproduce" name="steps_to_reproduce" rows="3"
                                                      placeholder="Step-by-step instructions...">{{ old('steps_to_reproduce', $ticket->steps_to_reproduce) }}</textarea>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-list-ol"></i>
                                                Provide clear, numbered steps to help reproduce the issue
                                            </small>
                                        </div>

                                        <!-- Actual Bug Description -->
                                        <div class="col-md-12 mb-3">
                                            <label for="actual_bug">Actual Bug Description <span class="text-danger">*</span></label>
                                            <textarea class="form-control actual_bug" 
                                                      id="actual_bug" name="actual_bug" rows="4"
                                                      placeholder="Describe the bug...">{{ old('actual_bug', $ticket->description) }}</textarea>
                                            <small class="form-text text-muted">Minimum 10 characters required</small>
                                        </div>

                                        <!-- Upload Files -->
                                        <div class="col-md-12 mb-3">
                                            <label for="uploaded_files">Upload Files</label>
                                            <input type="file" class="form-control uploaded_files" 
                                                   id="uploaded_files" name="uploaded_files" 
                                                   accept=".jpg,.jpeg,.png,.pdf,.docx">
                                            @if(!empty($ticket->uploaded_files))
                                                <div class="mt-2">
                                                    <small>Current file: 
                                                        <a href="{{ asset($ticket->uploaded_files) }}" target="_blank">
                                                            {{ basename($ticket->uploaded_files) }}
                                                        </a>
                                                    </small>
                                                </div>
                                            @endif
                                            <small class="form-text text-muted">
                                                <i class="fas fa-paperclip"></i>
                                                Allowed formats: .jpg, .jpeg, .png, .pdf, .docx (Max: 10MB)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12 mt-4">
                                <button class="btn btn-primary btn-lg" type="submit" id="submitBtn">
                                    <i class="fas fa-save"></i> Update Bug Details
                                </button>
                                <a href="{{ route('testing.index') }}" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    // Initialize Select2
    $('#project_id, #assigned_to').select2();

    function formatEmployeeOption(option) {
        if (!option.id) return option.text;
        const data = $(option.element).data();
        if (data.image) {
            const img = $('<img>', {
                src: data.image,
                style: 'width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;',
                onerror: "this.src='{{ asset('assets/img/default-avatar.png') }}'"
            });
            const span = $('<span>').text(option.text);
            return $('<span>').append(img).append(span);
        }
        return option.text;
    }

    $('#project_id').on('change', function() {
        const projectId = $(this).val();
        const assignSelect = $('#assigned_to');
        
        assignSelect.empty().append('<option value="">Loading team members...</option>');
        $('#assigneePreview').html('');
        
        if (projectId) {
            $.ajax({
                url: '{{ route("testing.getProjectTeamMembers", ":id") }}'.replace(':id', projectId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    assignSelect.empty().append('<option value="">Select Team Member (Optional)</option>');
                    
                    if (response && response.length > 0) {
                        $.each(response, function(index, member) {
                            const profileImg = member.profile_image ? '{{ asset("") }}' + member.profile_image : '{{ asset("assets/img/default-avatar.png") }}';
                            const option = $('<option></option>')
                                .attr('value', member.id)
                                .text((member.firstname || '') + ' ' + (member.lastname || ''))
                                .data('image', profileImg);
                            assignSelect.append(option);
                        });
                        
                        assignSelect.select2({
                            templateResult: formatEmployeeOption,
                            templateSelection: formatEmployeeOption,
                            escapeMarkup: function(markup) { return markup; }
                        });
                    }
                }
            });
        }
    });

    $('#assigned_to').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const preview = $('#assigneePreview');
        preview.html('');
        
        if (selectedOption.val()) {
            const imgUrl = selectedOption.data('image');
            const name = selectedOption.text();
            const img = $('<img>', {
                src: imgUrl,
                style: 'width: 50px; height: 50px; border-radius: 50%; border: 2px solid #007bff;',
                class: 'shadow-sm me-2',
                onerror: "this.src='{{ asset('assets/img/default-avatar.png') }}'"
            });
            const nameDiv = $('<span>', {
                class: 'fw-bold text-primary',
                text: 'Assigned to: ' + name
            });
            preview.append($('<div class="d-flex align-items-center mt-2"></div>').append(img).append(nameDiv));
        }
    });

    $('#ticketForm').on('submit', function(e) {
        const description = $('#actual_bug').val().trim();
        if (description.length < 10) {
            e.preventDefault();
            alert('Description must be at least 10 characters long.');
            $('#actual_bug').focus();
            return;
        }
    });
});
</script>

<style>
.bug-entry {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}
.bug-entry .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}
</style>
@endsection

