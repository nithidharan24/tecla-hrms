@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Create New Testing Ticket</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('testing.index') }}" class="breadcrumb-link">Tickets</a></li>
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
                    <h4 class="card-header">Create New Testing Ticket</h4>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
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

                        <form id="ticketForm" method="POST" action="{{ route('testing.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Project Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="project_id">Project <span class="text-danger">*</span></label>
                                    <select id="project_id" name="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->projectname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Priority -->
                                <div class="col-md-6 mb-3">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select id="priority" name="priority" class="form-control @error('priority') is-invalid @enderror" required>
                                        <option value="">Select Priority</option>
                                        <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Medium" {{ old('priority') === 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="Low" {{ old('priority') === 'Low' ? 'selected' : '' }}>Low</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- Assign To -->
                                <div class="col-md-12 mb-3">
                                    <label for="assigned_to">Assign To</label>
                                    <select id="assigned_to" name="assigned_to" class="form-control">
                                        <option value="">Select Team Member (Optional)</option>
                                    </select>
                                    <div id="assigneePreview" class="mt-2"></div>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Please select a project first to see available team members.
                                    </small>
                                </div>
                            </div>

                            <!-- Bugs Section -->
                            <div class="bugs-section">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="text-primary">Bug Details</h5>
                                            <button type="button" id="addBugBtn" class="btn btn-success btn-sm">
                                                <i class="fas fa-plus"></i> Add Another Bug
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bugs Container -->
                                <div id="bugs-container">
                                    <!-- Initial Bug Entry -->
                                    <div class="bug-entry card mb-4" data-index="0">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">Bug #1</h6>
                                            <button type="button" class="btn btn-danger btn-sm remove-bug" style="display: none;">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Module Name -->
                                                <div class="col-md-12 mb-3">
                                                    <label>Module Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control module_name" 
                                                           name="bugs[0][module_name]" 
                                                           value="{{ old('bugs.0.module_name') }}"
                                                           placeholder="Enter module name" required>
                                                    <small class="form-text text-muted">Specify the module where the bug was found</small>
                                                </div>

                                                <!-- Steps to Reproduce -->
                                                <div class="col-md-12 mb-3">
                                                    <label>Steps to Reproduce</label>
                                                    <textarea class="form-control steps_to_reproduce" 
                                                              name="bugs[0][steps_to_reproduce]" 
                                                              rows="3"
                                                              placeholder="Step-by-step instructions...">{{ old('bugs.0.steps_to_reproduce') }}</textarea>
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-list-ol"></i>
                                                        Provide clear, numbered steps to help reproduce the issue
                                                    </small>
                                                </div>

                                                <!-- Actual Bug -->
                                                <div class="col-md-12 mb-3">
                                                    <label>Actual Bug Description <span class="text-danger">*</span></label>
                                                    <textarea class="form-control actual_bug" 
                                                              name="bugs[0][description]" 
                                                              rows="4"
                                                              placeholder="Describe the bug..." required>{{ old('bugs.0.description') }}</textarea>
                                                    <small class="form-text text-muted">Minimum 10 characters required</small>
                                                </div>

                                                <!-- Upload Files -->
                                                <div class="col-md-12 mb-3">
                                                    <label>Upload Files</label>
                                                    <input type="file" class="form-control uploaded_files" 
                                                           name="bugs[0][uploaded_files]" 
                                                           accept=".jpg,.jpeg,.png,.pdf,.docx">
                                                    <small class="form-text text-muted">
                                                        <i class="fas fa-paperclip"></i>
                                                        Allowed formats: .jpg, .jpeg, .png, .pdf, .docx (Max: 10MB)
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12 mt-4">
                                <button class="btn btn-primary btn-lg" type="submit" id="submitBtn">
                                    <i class="fas fa-plus-circle"></i> Create Testing Ticket
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

<!-- Include jQuery and Select2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
$(document).ready(function() {
    let bugCount = 1;

    // Initialize Select2
    $('#project_id, #assigned_to').select2();

    // Project change event
    $('#project_id').on('change', function() {
        const projectId = $(this).val();
        const assignSelect = $('#assigned_to');
        
        assignSelect.empty().append('<option value="">Loading...</option>');
        $('#assigneePreview').html('');
        
        if (projectId) {
            $.ajax({
                url: '{{ route("testing.getProjectTeamMembers", ":id") }}'.replace(':id', projectId),
                type: 'GET',
                success: function(response) {
                    assignSelect.empty().append('<option value="">Select Team Member (Optional)</option>');
                    
                    if (response && response.length > 0) {
                        $.each(response, function(index, member) {
                            const name = (member.firstname || '') + ' ' + (member.lastname || '');
                            assignSelect.append(`<option value="${member.id}">${name}</option>`);
                        });
                    }
                },
                error: function() {
                    assignSelect.empty().append('<option value="">Error loading members</option>');
                }
            });
        } else {
            assignSelect.empty().append('<option value="">Select Team Member (Optional)</option>');
        }
    });

    // Add Bug Button Click
    $('#addBugBtn').on('click', function() {
        const template = `
            <div class="bug-entry card mb-4" data-index="${bugCount}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Bug #${bugCount + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-bug">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label>Module Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control module_name" 
                                   name="bugs[${bugCount}][module_name]" 
                                   placeholder="Enter module name" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Steps to Reproduce</label>
                            <textarea class="form-control steps_to_reproduce" 
                                      name="bugs[${bugCount}][steps_to_reproduce]" 
                                      rows="3"
                                      placeholder="Step-by-step instructions..."></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Actual Bug Description <span class="text-danger">*</span></label>
                            <textarea class="form-control actual_bug" 
                                      name="bugs[${bugCount}][description]" 
                                      rows="4"
                                      placeholder="Describe the bug..." required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Upload Files</label>
                            <input type="file" class="form-control uploaded_files" 
                                   name="bugs[${bugCount}][uploaded_files]" 
                                   accept=".jpg,.jpeg,.png,.pdf,.docx">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#bugs-container').append(template);
        bugCount++;
        
        // Show remove buttons on first bug if there are multiple
        if (bugCount > 1) {
            $('.bug-entry').first().find('.remove-bug').show();
        }
    });

    // Remove Bug
    $(document).on('click', '.remove-bug', function() {
        if ($('.bug-entry').length > 1) {
            $(this).closest('.bug-entry').remove();
            
            // Renumber bugs
            $('.bug-entry').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('h6').text(`Bug #${index + 1}`);
                
                // Update all input names
                $(this).find('input, textarea').each(function() {
                    const name = $(this).attr('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        $(this).attr('name', newName);
                    }
                });
            });
            
            bugCount = $('.bug-entry').length;
            
            // Hide remove button on first bug if only one remains
            if (bugCount === 1) {
                $('.bug-entry').first().find('.remove-bug').hide();
            }
        }
    });
});
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

.bug-entry {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
}

.bug-entry .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

.remove-bug {
    font-size: 0.75rem;
}
</style>
@endsection