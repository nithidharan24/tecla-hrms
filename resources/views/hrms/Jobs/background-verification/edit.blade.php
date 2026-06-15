@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Background Verification</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('background-verification.index') }}">Background Verification</a></li>
                    <li class="breadcrumb-item active">Edit Record</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Verification Details</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('background-verification.update', $bgvRecord->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Candidate <span class="text-danger">*</span></label>
                                    <select name="candidate_id" id="candidate_id" class="form-control @error('candidate_id') is-invalid @enderror" required>
                                        <option value="">Select Candidate</option>
                                        @foreach($candidates as $candidate)
                                            <option value="{{ $candidate->id }}" 
                                                {{ (old('candidate_id', $bgvRecord->candidate_id) == $candidate->id) ? 'selected' : '' }}>
                                                {{ $candidate->first_name }} {{ $candidate->last_name }} - {{ $candidate->position_applied }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('candidate_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="not_started" {{ old('status', $bgvRecord->status) == 'not_started' ? 'selected' : '' }}>BGV Not Yet Done</option>
                                        <option value="in_progress" {{ old('status', $bgvRecord->status) == 'in_progress' ? 'selected' : '' }}>BGV In Progress</option>
                                        <option value="completed" {{ old('status', $bgvRecord->status) == 'completed' ? 'selected' : '' }}>BGV Completed</option>
                                        <option value="failed" {{ old('status', $bgvRecord->status) == 'failed' ? 'selected' : '' }}>BGV Failed</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Verification Date</label>
                                    <input type="date" 
                                           name="verification_date" 
                                           id="verification_date" 
                                           class="form-control @error('verification_date') is-invalid @enderror" 
                                           value="{{ old('verification_date', $bgvRecord->verification_date) }}">
                                    @error('verification_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Upload Verification Document</label>
                                    <input type="file" 
                                           name="document_file" 
                                           id="document_file" 
                                           class="form-control @error('document_file') is-invalid @enderror" 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="form-text text-muted">
                                        Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 2MB)
                                    </small>
                                    @error('document_file')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror

                                    @if(!empty($bgvRecord->documents) && isset($bgvRecord->documents[0]['file_path']))
                                        <div class="mt-2">
                                            <p class="mb-1">Current Document:</p>
                                            <a href="{{ asset('storage/'.$bgvRecord->documents[0]['file_path']) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-file"></i> View Document
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" 
                                              id="remarks" 
                                              class="form-control @error('remarks') is-invalid @enderror" 
                                              rows="4">{{ old('remarks', $bgvRecord->remarks) }}</textarea>
                                    @error('remarks')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Update Record</button>
                            <a href="{{ route('background-verification.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
