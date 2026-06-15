@extends('layouts.index')

@section('content')
<div class="content container-fluid mt-3">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Interview Feedback Form
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Candidate:</strong> {{ $interview->first_name }} {{ $interview->last_name }}<br>
                        <strong>Job Position:</strong> {{ $interview->job_title }}<br>
                        <strong>Interview Round:</strong> {{ ucwords(str_replace(['_', 'status'], [' ', ''], $interview->interview_round)) }}<br>
                        <strong>Interview Date:</strong> {{ \Carbon\Carbon::parse($interview->interview_datetime)->format('d-m-Y H:i') }}
                    </div>

                    <form action="{{ route('interview-feedback.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="interview_id" value="{{ $interview->id }}">
                        <input type="hidden" name="candidate_id" value="{{ $interview->candidate_id }}">
                        <input type="hidden" name="interview_round" value="{{ $interview->interview_round }}">

                        <h6 class="mt-4 mb-3"><strong>Scoring (1-10)</strong></h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Technical Score <span class="text-danger">*</span></label>
                                <input type="number" name="technical_score" class="form-control" min="1" max="10" required>
                                @error('technical_score')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Communication Score <span class="text-danger">*</span></label>
                                <input type="number" name="communication_score" class="form-control" min="1" max="10" required>
                                @error('communication_score')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Problem Solving Score <span class="text-danger">*</span></label>
                                <input type="number" name="problem_solving_score" class="form-control" min="1" max="10" required>
                                @error('problem_solving_score')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Domain Knowledge Score <span class="text-danger">*</span></label>
                                <input type="number" name="domain_knowledge_score" class="form-control" min="1" max="10" required>
                                @error('domain_knowledge_score')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Overall Rating <span class="text-danger">*</span></label>
                                <input type="number" name="overall_rating" class="form-control" min="1" max="10" required>
                                @error('overall_rating')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <h6 class="mt-4 mb-3"><strong>Detailed Feedback</strong></h6>

                        <div class="mb-3">
                            <label>Strengths <span class="text-danger">*</span></label>
                            <textarea name="strengths" class="form-control" rows="3" required></textarea>
                            @error('strengths')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Weaknesses <span class="text-danger">*</span></label>
                            <textarea name="weaknesses" class="form-control" rows="3" required></textarea>
                            @error('weaknesses')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Interview Notes <span class="text-danger">*</span></label>
                            <textarea name="interview_notes" class="form-control" rows="4" required></textarea>
                            @error('interview_notes')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label>Recommendation <span class="text-danger">*</span></label>
                            <select name="recommendation" class="form-control" required>
                                <option value="">Select Recommendation</option>
                                <option value="strongly_recommended">Strongly Recommended</option>
                                <option value="recommended">Recommended</option>
                                <option value="maybe">Maybe</option>
                                <option value="not_recommended">Not Recommended</option>
                                <option value="reject">Reject</option>
                            </select>
                            @error('recommendation')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i>Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
