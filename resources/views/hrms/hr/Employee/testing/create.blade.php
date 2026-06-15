@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Create New Testing</h2>
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
                    <h4 class="card-header">Create New Ticket</h4>
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

                        <form id="ticketForm" method="POST" action="{{ route('testing.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Hidden fields to store bug data -->
                            <input type="hidden" name="module_name" id="hidden_module_name">
                            <input type="hidden" name="description" id="hidden_description">
                            <input type="hidden" name="steps_to_reproduce" id="hidden_steps_to_reproduce">
                            
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

                                <!-- Bug Entry Template (Hidden) -->
                                <div id="bugTemplate" class="bug-entry card mb-4" style="display: none;">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Bug #<span class="bug-number">1</span></h6>
                                        <button type="button" class="btn btn-danger btn-sm remove-bug">
                                            <i class="fas fa-times"></i> Remove
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Module Name -->
                                            <div class="col-md-12 mb-3">
                                                <label>Module Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control module_name" name="module_name" placeholder="Enter module name">
                                            </div>

                                            <!-- Steps to Reproduce -->
                                            <div class="col-md-12 mb-3">
                                                <label>Steps to Reproduce</label>
                                                <textarea class="form-control steps_to_reproduce" name="steps_to_reproduce" rows="3" placeholder="Step-by-step instructions..."></textarea>
                                            </div>

                                            <!-- Actual Bug -->
                                            <div class="col-md-12 mb-3">
                                                <label>Actual Bug Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control actual_bug" name="description" rows="4" placeholder="Describe the bug..."></textarea>
                                            </div>

                                            <!-- Upload Files -->
                                            <div class="col-md-12 mb-3">
                                                <label>Upload Files</label>
                                                <input type="file" class="form-control uploaded_files" name="uploaded_files" accept=".jpg,.jpeg,.png,.pdf,.docx">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Initial Bug Entry -->
                                <div class="bug-entry card mb-4" id="initialBug">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Bug #1</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Module Name -->
                                            <div class="col-md-12 mb-3">
                                                <label for="module_name_1">Module Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control module_name" 
                                                       id="module_name_1" name="module_name" 
                                                       value="{{ old('module_name') }}" 
                                                       placeholder="Enter module name" required>
                                                @error('module_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Specify the module where the bug was found</small>
                                            </div>

                                            <!-- Steps to Reproduce -->
                                            <div class="col-md-12 mb-3">
                                                <label for="steps_to_reproduce_1">Steps to Reproduce</label>
                                                <textarea class="form-control steps_to_reproduce" 
                                                          id="steps_to_reproduce_1" name="steps_to_reproduce" rows="3"
                                                          placeholder="Step-by-step instructions...">{{ old('steps_to_reproduce') }}</textarea>
                                                @error('steps_to_reproduce')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-list-ol"></i>
                                                    Provide clear, numbered steps to help reproduce the issue
                                                </small>
                                            </div>

                                            <!-- Actual Bug -->
                                            <div class="col-md-12 mb-3">
                                                <label for="actual_bug_1">Actual Bug Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control actual_bug" 
                                                          id="actual_bug_1" name="description" rows="4"
                                                          placeholder="Describe the bug..." required>{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Minimum 10 characters required</small>
                                            </div>

                                            <!-- Upload Files -->
                                            <div class="col-md-12 mb-3">
                                                <label for="uploaded_files_1">Upload Files</label>
                                                <input type="file" class="form-control uploaded_files" 
                                                       id="uploaded_files_1" name="uploaded_files" 
                                                       accept=".jpg,.jpeg,.png,.pdf,.docx">
                                                @error('uploaded_files')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">
                                                    <i class="fas fa-paperclip"></i>
                                                    Allowed formats: .jpg, .jpeg, .png, .pdf, .docx (Max: 10MB)
                                                </small>
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

    // Initialize Select2 for better dropdowns
    $('#project_id, #assigned_to').select2();

    // Custom formatting for employee options
    function formatEmployeeOption(option) {
        if (!option.id) {
            return option.text;
        }
        
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
        
        // Clear current options
        assignSelect.empty().append('<option value="">Loading team members...</option>');
        $('#assigneePreview').html('');
        
        if (projectId) {
            $.ajax({
                url: '{{ route("testing.getProjectTeamMembers", ":id") }}'.replace(':id', projectId),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Team members fetched:", response);
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
                        
                        // Reinitialize Select2 with custom formatting
                        assignSelect.select2({
                            templateResult: formatEmployeeOption,
                            templateSelection: formatEmployeeOption,
                            escapeMarkup: function(markup) { return markup; }
                        });
                    } else {
                        assignSelect.append('<option value="" disabled>No team members found</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching team members:", error, xhr);
                    assignSelect.empty().append('<option value="" disabled>Error loading team members</option>');
                }
            });
        } else {
            assignSelect.empty().append('<option value="">Select Team Member (Optional)</option>');
        }
    });

    // Show assignee preview
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

    // Handle form submission - copy data from bug fields to hidden fields
    $('#ticketForm').on('submit', function(e) {
        // Get values from the first bug entry
        const moduleName = $('#module_name_1').val();
        const description = $('#actual_bug_1').val();
        const steps = $('#steps_to_reproduce_1').val();
        
        // Set values to hidden fields
        $('#hidden_module_name').val(moduleName);
        $('#hidden_description').val(description);
        $('#hidden_steps_to_reproduce').val(steps);
        
        console.log('Submitting form with:', {
            module_name: moduleName,
            description: description,
            steps_to_reproduce: steps
        });
        
        return true; // Continue with submission
    });

    // Add multiple bugs functionality (if needed in future)
    $('#addBugBtn').on('click', function() {
        bugCount++;
        const template = $('#bugTemplate').clone();
        
        template.removeAttr('id').show();
        template.find('.bug-number').text(bugCount);
        
        // Update all field names with unique names
        template.find('input, textarea').each(function() {
            const $this = $(this);
            const name = $this.attr('name');
            if (name) {
                $this.attr('name', name + '_' + bugCount);
            }
            
            // Update IDs to prevent duplication
            const id = $this.attr('id');
            if (id) {
                $this.attr('id', id + '_' + bugCount);
            }
            
            $this.val('');
        });
        
        // Update labels
        template.find('label').each(function() {
            const $this = $(this);
            const forAttr = $this.attr('for');
            if (forAttr) {
                $this.attr('for', forAttr + '_' + bugCount);
            }
        });
        
        $('#initialBug').after(template);
        
        // Add remove functionality
        template.find('.remove-bug').on('click', function() {
            if ($('.bug-entry').length > 1) {
                $(this).closest('.bug-entry').remove();
                updateBugNumbers();
            } else {
                alert('At least one bug entry is required.');
            }
        });
    });

    // Update bug numbers after removal
    function updateBugNumbers() {
        $('.bug-entry').each(function(index) {
            const bugNumber = index + 1;
            $(this).find('.bug-number').text(bugNumber);
            $(this).find('h6').text('Bug #' + bugNumber);
        });
    }
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

.badge {
    font-size: 0.875em;
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

.form-control.is-invalid {
    border-color: #dc3545;
}
</style>
@endsection