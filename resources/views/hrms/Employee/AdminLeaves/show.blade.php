@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h3 class="page-title mb-1">Leave Details</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin-leaves.index') }}">Leaves</a></li>
                    <li class="breadcrumb-item active" aria-current="page">View Leave</li>
                </ol>
            </nav>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admin-leaves.index') }}" class="btn btn-outline-primary">
                <i class="fa fa-arrow-left me-1"></i> Back to Leaves
            </a>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Leave Details Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Employee Info -->
            <div class="row g-4">
                <!-- Left Side -->
                <div class="col-md-6">
                    <h5 class="section-title mb-3">Employee Information</h5>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset($leave->profile_image ?? 'assets/img/default-user.jpg') }}" 
                             class="rounded-circle border me-3" height="80" width="80" alt="{{ $leave->employee_name }}">
                        <div>
                            <p class="mb-1"><strong>{{ $leave->employee_name }}</strong></p>
                            <small class="text-muted">Employee ID: {{ $leave->employeeid }}</small><br>
                            <small class="text-muted">{{ $leave->designation_name }}</small>
                        </div>
                    </div>
                </div>
                <!-- Right Side -->
                <div class="col-md-6">
                    <h5 class="section-title mb-3">Contact Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="fa fa-envelope text-primary me-2"></i>{{ $leave->email }}</li>
                        <li><i class="fa fa-phone text-primary me-2"></i>{{ $leave->phone }}</li>
                    </ul>
                </div>
            </div>

            <hr class="my-4">

            <!-- Leave Info -->
            <div class="row g-4">
                <div class="col-md-12">
                    <h5 class="section-title mb-3">Leave Details</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">Leave Type</label>
                            <div>{{ $leave->leave_type }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">From Date</label>
                            <div>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">To Date</label>
                            <div>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">Number of Days</label>
                            <div>{{ $noOfDays }} days</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">Status</label>
                            <div>
                                <span class="badge bg-{{ 
                                    $leave->status == 'approved' ? 'success' : 
                                    ($leave->status == 'declined' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted fw-semibold">Applied On</label>
                            <div>{{ \Carbon\Carbon::parse($leave->created_at)->format('d M Y h:i A') }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted fw-semibold">Reason</label>
                            <div class="border rounded p-2 bg-light">{{ $leave->leave_reason }}</div>
                        </div>

                        <!-- Medical Certificate -->
                        <div class="col-12">
                            <label class="text-muted fw-semibold">Medical Certificate</label>
                            @if($leave->medical_certificate)
                                <div class="btn-group mt-1" role="group">
                                    
                                    <a href="{{ route('admin-leaves.download-medical-certificate', $leave->id) }}" 
                                       class="btn btn-success btn-sm" target="_blank">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                            data-bs-toggle="tooltip" 
                                            title="{{ $fileInfo }}">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                </div>
                            @else
                                <div class="text-muted">No medical certificate uploaded</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Certificate Modal -->
    <div class="modal fade" id="medicalCertificateModal" tabindex="-1" aria-labelledby="medicalCertificateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="medicalCertificateModalLabel">Medical Certificate Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="filePreviewContent">
                    <!-- Loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadLink" class="btn btn-success">
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewMedicalCertificate(viewUrl) {
    $('#filePreviewContent').html(`
        <div class="py-5 text-center">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Loading document...</p>
        </div>
    `);
    $('#downloadLink').attr('href', viewUrl.replace('/view/', '/download/'));
    $('#medicalCertificateModal').modal('show');

    const fileExt = viewUrl.split('.').pop().toLowerCase();
    let content = '';

    if (fileExt === 'pdf') {
        content = `<iframe src="${viewUrl}" width="100%" height="600px" frameborder="0"></iframe>`;
    } else if (['jpg','jpeg','png','gif'].includes(fileExt)) {
        content = `<img src="${viewUrl}" class="img-fluid" style="max-height: 600px;">`;
    } else if (['doc','docx'].includes(fileExt)) {
        content = `
            <div class="alert alert-info">
                <h5><i class="fa fa-file-word"></i> Word Document</h5>
                Download to view: 
                <a href="${viewUrl.replace('/view/', '/download/')}" class="btn btn-primary btn-sm ms-2">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>`;
    } else {
        content = `
            <div class="alert alert-warning">
                Preview not available. Please download the file.
                <a href="${viewUrl.replace('/view/', '/download/')}" class="btn btn-primary btn-sm ms-2">
                    <i class="fa fa-download"></i> Download
                </a>
            </div>`;
    }
    $('#filePreviewContent').html(content);
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    border-left: 4px solid #007bff;
    padding-left: 10px;
}
.card {
    border-radius: 10px;
}
.breadcrumb-item + .breadcrumb-item::before {
    content: ">";
}
</style>
@endpush
