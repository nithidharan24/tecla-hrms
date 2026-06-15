@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Bug</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('testing.index') }}" class="breadcrumb-link">Testing Tickets</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('testing.show', $ticket->id) }}" class="breadcrumb-link">{{ $ticket->testing_ticket_id }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Bug</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-bug"></i> Edit Bug #{{ $bug->id }}
                        </h5>
                        <span class="badge bg-info">Ticket: {{ $ticket->testing_ticket_id }}</span>
                    </div>
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

                        <form method="POST" action="{{ route('testing.bug.update', $bug->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Ticket Info (Read Only) -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="bg-light p-3 rounded">
                                        <h6 class="text-primary mb-3">Ticket Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Ticket ID</small>
                                                <strong>{{ $ticket->testing_ticket_id }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Project</small>
                                                <strong>{{ $ticket->projectname ?? 'N/A' }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Priority</small>
                                                <span class="badge bg-{{ $ticket->priority == 'High' ? 'danger' : ($ticket->priority == 'Medium' ? 'warning' : 'success') }}">
                                                    {{ $ticket->priority }}
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted d-block">Current Bug Status</small>
                                                <span class="badge bg-{{ 
                                                    $bug->status === 'Closed' ? 'success' : 
                                                    ($bug->status === 'Open' ? 'primary' : 
                                                    ($bug->status === 'In Progress' ? 'warning' : 
                                                    ($bug->status === 'Resolved' ? 'info' : 'danger'))) 
                                                }}">
                                                    {{ $bug->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Module Name -->
                                <div class="col-md-12 mb-3">
                                    <label for="module_name">Module Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('module_name') is-invalid @enderror" 
                                           id="module_name" name="module_name" 
                                           value="{{ old('module_name', $bug->module_name) }}" 
                                           placeholder="Enter module name" required>
                                    @error('module_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Specify the module where the bug was found</small>
                                </div>

                                <!-- Steps to Reproduce -->
                                <div class="col-md-12 mb-3">
                                    <label for="steps_to_reproduce">Steps to Reproduce</label>
                                    <textarea class="form-control @error('steps_to_reproduce') is-invalid @enderror" 
                                              id="steps_to_reproduce" name="steps_to_reproduce" 
                                              rows="4" placeholder="Step-by-step instructions...">{{ old('steps_to_reproduce', $bug->steps_to_reproduce) }}</textarea>
                                    @error('steps_to_reproduce')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-list-ol"></i>
                                        Provide clear, numbered steps to help reproduce the issue
                                    </small>
                                </div>

                                <!-- Actual Bug Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description">Actual Bug Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" 
                                              rows="5" placeholder="Describe the bug..." required>{{ old('description', $bug->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 10 characters required</small>
                                </div>

                                <!-- Current Attachment (if exists) -->
                                @if($bug->uploaded_files)
                                <div class="col-md-12 mb-3">
                                    <label>Current Attachment</label>
                                    <div class="border p-3 rounded bg-light">
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-file-o fa-2x text-primary me-3"></i>
                                            <div>
                                                <div class="fw-bold">{{ basename($bug->uploaded_files) }}</div>
                                                <a href="{{ asset($bug->uploaded_files) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                    <i class="fa fa-eye"></i> View File
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Upload New Files -->
                                <div class="col-md-12 mb-3">
                                    <label for="uploaded_files">
                                        @if($bug->uploaded_files)
                                            Replace Attachment (Optional)
                                        @else
                                            Upload Files (Optional)
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('uploaded_files') is-invalid @enderror" 
                                           id="uploaded_files" name="uploaded_files" 
                                           accept=".jpg,.jpeg,.png,.pdf,.docx">
                                    @error('uploaded_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="fas fa-paperclip"></i>
                                        Allowed formats: .jpg, .jpeg, .png, .pdf, .docx (Max: 10MB)
                                    </small>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> Update Bug
                                    </button>
                                    <a href="{{ route('testing.show', $ticket->id) }}" class="btn btn-secondary btn-lg ms-2">
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

<script>
$(document).ready(function() {
    // Preview file name when selected
    $('#uploaded_files').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            // You can add a preview here if needed
            console.log('Selected file:', fileName);
        }
    });
});
</script>

<style>
.bg-light {
    background-color: #f8f9fa !important;
}
.rounded {
    border-radius: 0.25rem !important;
}
.border {
    border: 1px solid #dee2e6 !important;
}
</style>
@endsection