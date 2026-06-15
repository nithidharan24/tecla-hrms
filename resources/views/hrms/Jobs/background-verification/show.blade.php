@extends('layouts.index')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <i class="mdi mdi-file-document-box-outline text-info"></i> Background Verification Details
                    </h4>
                    
                    <div class="mt-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Candidate Name:</strong>
                                <p>{{ $verification->candidate->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
                                    @if($verification->status == 'not_started')
                                        <span class="badge bg-secondary">Not Started</span>
                                    @elseif($verification->status == 'in_progress')
                                        <span class="badge bg-warning text-dark">In Progress</span>
                                    @elseif($verification->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($verification->status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Assigned To:</strong>
                                <p>{{ $verification->assigned_to ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Start Date:</strong>
                                <p>{{ $verification->start_date ? \Carbon\Carbon::parse($verification->start_date)->format('d M Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Completion Date:</strong>
                                <p>{{ $verification->completion_date ? \Carbon\Carbon::parse($verification->completion_date)->format('d M Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Remarks:</strong>
                                <p>{{ $verification->remarks ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Document:</strong><br>
                                @if($verification->document)
                                    <a href="{{ asset('uploads/background-verification/' . $verification->document) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="mdi mdi-file-eye"></i> View Document
                                    </a>
                                @else
                                    <p>No document uploaded.</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('background-verification.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('background-verification.edit', $verification->id) }}" class="btn btn-primary">
                                <i class="mdi mdi-pencil"></i> Edit Record
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
