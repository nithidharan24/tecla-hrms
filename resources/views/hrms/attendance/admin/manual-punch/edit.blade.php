@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Edit Manual Punch Request</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('attendance.index') }}">Attendance</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('manual-punch.index') }}">Manual Punch Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Request</li>
                    </ol>
                </nav>
            </div>
            <div class="col-auto">
                <a href="{{ route('manual-punch.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Edit Request Details</h5>
                    <p class="text-muted small mb-0">Request #{{ $request->id }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('manual-punch.update', $request->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Request Info (Readonly) -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Employee</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $employee->firstname }} {{ $employee->lastname }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Type</label>
                                    <input type="text" class="form-control" 
                                           value="{{ ucwords(str_replace('_', ' ', $request->request_type)) }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Date</label>
                                    <input type="text" class="form-control" 
                                           value="{{ \Carbon\Carbon::parse($request->request_date)->format('d M Y') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Request Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="request_time" 
                                           value="{{ \Carbon\Carbon::parse($request->request_time)->format('H:i') }}" required>
                                    <small class="text-muted">Current: {{ \Carbon\Carbon::parse($request->request_time)->format('h:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <div class="form-group">
                                <label class="form-label">Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="reason" rows="4" required>{{ $request->reason }}</textarea>
                                <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                            </div>
                        </div>

                        <!-- Timestamps -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Created:</strong> {{ \Carbon\Carbon::parse($request->created_at)->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Last Updated:</strong> {{ \Carbon\Carbon::parse($request->updated_at)->format('d M Y, h:i A') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manual-punch.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Warning -->
            <div class="card border-0 shadow-sm mt-4 border-warning">
                <div class="card-header bg-white border-warning">
                    <h6 class="card-title mb-0 text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Important</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>You can only edit pending requests</li>
                        <li>Requests can only be edited within 1 hour of creation</li>
                        <li>Once approved or rejected, requests cannot be modified</li>
                        <li>Make sure the information is accurate before updating</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Form validation
        $('form').on('submit', function(e) {
            const reason = $('textarea[name="reason"]').val().trim();
            if (reason.length < 10) {
                e.preventDefault();
                alert('Reason must be at least 10 characters');
                return false;
            }
        });
    });
</script>
@endsection