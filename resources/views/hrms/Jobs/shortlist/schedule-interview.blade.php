@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Schedule Interview</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shortlist.index') }}">Shortlisted Candidates</a></li>
                    <li class="breadcrumb-item active">Schedule Interview</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Candidate Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="candidate-info-card">
                <div class="candidate-avatar">
                    <span>{{ strtoupper(substr($candidate->first_name, 0, 1)) }}{{ strtoupper(substr($candidate->last_name, 0, 1)) }}</span>
                </div>
                <div class="candidate-details">
                    <h4>{{ $candidate->first_name }} {{ $candidate->last_name }}</h4>
                    <div class="candidate-meta">
                        <span><i class="fas fa-envelope"></i> {{ $candidate->email }}</span>
                        <span><i class="fas fa-briefcase"></i> {{ $candidate->position_applied }}</span>
                        <span><i class="fas fa-clock"></i> {{ $candidate->experience_years }} years exp.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Candidate Info Card -->

    <div class="row">
        <div class="col-md-12">
            <div class="card modern-card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Schedule Interview</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('shortlist.store-interview') }}" method="POST" id="interviewForm">
                        @csrf
                        <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Job Position <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control modern-input" value="{{ $candidate->position_applied }}" readonly>
                                    <input type="hidden" name="job_id" value="{{ $job->id ?? '' }}">
                                    <input type="hidden" id="jobDepartment" value="{{ $job->department ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Interview Round <span class="text-danger">*</span></label>
                                    <select class="form-select modern-select @error('interview_round') is-invalid @enderror" name="interview_round" id="interviewRound" required>
                                        <option value="">Select Round</option>
                                        <option value="hr_interview_status">HR Interview</option>
                                        <option value="technical_interview_status">Technical Interview</option>
                                        <option value="manager_round_status">Manager Round</option>
                                        <option value="final_round_status">Final Round</option>
                                    </select>
                                    @error('interview_round')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Interview Type <span class="text-danger">*</span></label>
                                    <select class="form-select modern-select @error('interview_type') is-invalid @enderror" name="interview_type" required>
                                        <option value="">Select Type</option>
                                        <option value="telephonic">Telephonic</option>
                                        <option value="face_to_face">Face to Face</option>
                                        <option value="video_call">Video Call</option>
                                    </select>
                                    @error('interview_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Interviewer Name <span class="text-danger">*</span></label>
                                    <select class="form-select modern-select" name="interviewer_employee_id" id="interviewerSelect" required disabled>
                                        <option value="">Select Interview Round First</option>
                                    </select>
                                    <small class="text-muted"><i class="fas fa-info-circle"></i> Select interview round to load interviewers</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Interviewer Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control modern-input" id="interviewerEmail" name="interviewer_email" readonly>
                                    <small class="text-muted"><i class="fas fa-info-circle"></i> Auto-filled from employee</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Interview Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control modern-input @error('interview_datetime') is-invalid @enderror" 
                                           name="interview_datetime" value="{{ old('interview_datetime') }}" required>
                                    @error('interview_datetime')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Available Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control modern-input @error('availability_date') is-invalid @enderror"
                                           name="availability_date" value="{{ old('availability_date') }}" required>
                                    @error('availability_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Time Slot <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control modern-input @error('availability_time_slot') is-invalid @enderror"
                                           name="availability_time_slot" value="{{ old('availability_time_slot') }}"
                                           placeholder="10:00 AM - 11:00 AM" required>
                                    @error('availability_time_slot')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Additional Notes</label>
                                    <textarea class="form-control modern-input @error('notes') is-invalid @enderror" 
                                              name="notes" rows="4" placeholder="Add any special instructions or notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="submit-section mt-4">
                            <button class="btn btn-primary-modern me-2" type="submit">
                                <i class="fas fa-calendar-check me-2"></i>Schedule Interview
                            </button>
                            <a href="{{ route('shortlist.index') }}" class="btn btn-secondary-modern">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Interviews Table -->
    @if(isset($interviews) && $interviews->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card modern-card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-list me-2"></i>Scheduled Interview Rounds</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Round</th>
                                    <th>Interviewer</th>
                                    <th>Type</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Feedback</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interviews as $interview)
                                <tr>
                                    <td>
                                        @php
                                            $roundNames = [
                                                'hr_interview_status' => 'HR Interview',
                                                'technical_interview_status' => 'Technical Interview',
                                                'manager_round_status' => 'Manager Round',
                                                'final_round_status' => 'Final Round'
                                            ];
                                        @endphp
                                        <span class="badge bg-info">{{ $roundNames[$interview->interview_round] ?? $interview->interview_round }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $interview->interviewer_name }}</strong><br>
                                        <small class="text-muted">{{ $interview->interviewer_email }}</small>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $interview->interview_type)) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($interview->interview_datetime)->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <form action="{{ route('shortlist.interview.update', $interview->id) }}" method="POST" class="status-update-form">
                                            @csrf
                                            <select class="form-select form-select-sm status-select" name="status">
                                                <option value="scheduled" {{ $interview->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                <option value="completed" {{ $interview->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $interview->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="rescheduled" {{ $interview->status == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        @if($interview->feedback)
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $interview->id }}">
                                                View
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $interview->id }}">
                                                Add
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $interview->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Feedback Modal -->
                                <div class="modal fade" id="feedbackModal{{ $interview->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Interview Feedback</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('shortlist.interview.update', $interview->id) }}" method="POST" class="feedback-form">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select class="form-select" name="status" required>
                                                            <option value="scheduled" {{ $interview->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                            <option value="completed" {{ $interview->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="cancelled" {{ $interview->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            <option value="rescheduled" {{ $interview->status == 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Rating (1-5)</label>
                                                        <input type="number" class="form-control" name="rating" min="1" max="5" value="{{ $interview->rating }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Feedback</label>
                                                        <textarea class="form-control" name="feedback" rows="4">{{ $interview->feedback }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Feedback</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.content.container-fluid {
    background: #f5f6fb;
    min-height: calc(100vh - 60px);
    padding: 28px 24px;
}

.page-header {
    margin-bottom: 24px;
}

.page-title {
    color: #020b36;
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 4px;
}

.breadcrumb {
    color: #7b88aa;
    font-size: 14px;
}

.candidate-info-card {
    align-items: center;
    background: #fff;
    border: 1px solid #e1e5ef;
    border-bottom: 3px solid #ff6b13;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
    display: flex;
    gap: 18px;
    padding: 22px;
}

.candidate-avatar {
    align-items: center;
    background: #ff6b13;
    border-radius: 50%;
    color: #fff;
    display: flex;
    flex: 0 0 58px;
    font-size: 18px;
    font-weight: 800;
    height: 58px;
    justify-content: center;
    width: 58px;
}

.candidate-details h4 {
    color: #020b36;
    font-size: 22px;
    font-weight: 800;
    margin: 0 0 8px;
}

.candidate-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.candidate-meta span {
    align-items: center;
    color: #7b88aa;
    display: inline-flex;
    font-size: 14px;
    font-weight: 600;
    gap: 8px;
}

.candidate-meta i {
    color: #ff6b13;
}

.modern-card {
    background: #fff;
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(8, 24, 74, 0.04);
}

.modern-card .card-header {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    border-radius: 14px 14px 0 0;
    padding: 18px 22px;
}

.modern-card .card-title {
    color: #020b36;
    font-size: 18px;
    font-weight: 800;
}

.modern-card .card-title i {
    color: #ff6b13;
}

.modern-card .card-body {
    padding: 22px;
}

.form-label {
    color: #29344d;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 8px;
}

.modern-input,
.modern-select,
.feedback-form .form-control,
.feedback-form .form-select,
.status-select {
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    min-height: 44px;
    padding: 10px 14px;
}

.modern-input:focus,
.modern-select:focus,
.feedback-form .form-control:focus,
.feedback-form .form-select:focus,
.status-select:focus {
    border-color: #ff6b13;
    box-shadow: 0 0 0 0.2rem rgba(255, 107, 19, 0.12);
}

.text-muted {
    color: #7b88aa !important;
}

.text-muted i {
    color: #ff6b13;
}

.btn-primary-modern,
.modal-footer .btn-primary,
.table .btn-primary {
    background: #ff6b13;
    border: 1px solid #ff6b13;
    border-radius: 10px;
    color: #fff;
    font-weight: 800;
    padding: 11px 22px;
}

.btn-primary-modern:hover,
.modal-footer .btn-primary:hover,
.table .btn-primary:hover {
    background: #e85f0f;
    border-color: #e85f0f;
    color: #fff;
}

.btn-secondary-modern,
.modal-footer .btn-secondary,
.btn-outline-secondary,
.btn-outline-primary {
    background: #fff;
    border: 1px solid #dfe4ee;
    border-radius: 10px;
    color: #020b36;
    font-weight: 800;
    padding: 10px 20px;
}

.btn-secondary-modern:hover,
.modal-footer .btn-secondary:hover,
.btn-outline-secondary:hover,
.btn-outline-primary:hover {
    background: #fff8f3;
    border-color: #ff6b13;
    color: #ff6b13;
}

.table .btn-sm {
    align-items: center;
    display: inline-flex;
    gap: 6px;
    justify-content: center;
    min-height: 34px;
    padding: 7px 12px;
}

.table {
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 0;
}

.table thead th {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    color: #59647d;
    font-size: 12px;
    font-weight: 800;
    padding: 15px;
    text-transform: uppercase;
}

.table tbody td {
    border-top: 1px solid #edf0f5;
    color: #29344d;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #fff8f3;
}

.table strong {
    color: #020b36;
}

.badge {
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    padding: 7px 12px;
}

.badge.bg-info {
    background: #e8fbff !important;
    color: #078ca0;
}

.modal-content {
    border: 1px solid #e1e5ef;
    border-radius: 14px;
    box-shadow: 0 18px 45px rgba(8, 24, 74, 0.18);
}

.modal-header {
    background: #fbfcff;
    border-bottom: 1px solid #e1e5ef;
    border-radius: 14px 14px 0 0;
}

.modal-title {
    color: #020b36;
    font-weight: 800;
}

.submit-section {
    align-items: center;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

@media (max-width: 768px) {
    .content.container-fluid {
        padding: 20px 14px;
    }

    .candidate-info-card {
        align-items: flex-start;
        flex-direction: column;
    }
    
    .candidate-meta {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const interviewRoundSelect = document.getElementById('interviewRound');
    const interviewerSelect = document.getElementById('interviewerSelect');
    const interviewerEmail = document.getElementById('interviewerEmail');
    const jobDepartment = document.getElementById('jobDepartment').value;

    const showAlert = (icon, title, text) => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon,
                title,
                text,
                timer: icon === 'success' ? 1800 : undefined,
                showConfirmButton: icon !== 'success'
            });
            return;
        }

        alert(text || title);
    };

    @if(session('success'))
        showAlert('success', 'Success!', @json(session('success')));
    @endif

    @if(session('error'))
        showAlert('error', 'Error!', @json(session('error')));
    @endif
    
    // Load interviewers when round is selected
    interviewRoundSelect.addEventListener('change', function() {
        const round = this.value;
        
        console.log('Round selected:', round);
        console.log('Job Department:', jobDepartment);
        
        if (!round) {
            interviewerSelect.disabled = true;
            interviewerSelect.innerHTML = '<option value="">Select Interview Round First</option>';
            interviewerEmail.value = '';
            return;
        }
        
        // Show loading
        interviewerSelect.disabled = true;
        interviewerSelect.innerHTML = '<option value="">Loading...</option>';
        
        // Fetch interviewers using named route
        fetch('{{ route("shortlist.get-interviewers") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                round: round,
                job_department: jobDepartment
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                interviewerSelect.innerHTML = '<option value="">Select Interviewer</option>';
                
                if (data.interviewers.length === 0) {
                    interviewerSelect.innerHTML += '<option value="" disabled>No interviewers found</option>';
                    console.log('No interviewers found');
                } else {
                    console.log('Interviewers count:', data.interviewers.length);
                    
                    data.interviewers.forEach(interviewer => {
                        const role = interviewer.role ? ` (${interviewer.role})` : ` (${interviewer.employeeid})`;
                        const option = document.createElement('option');
                        option.value = interviewer.id;
                        option.setAttribute('data-email', interviewer.email);
                        option.textContent = `${interviewer.firstname} ${interviewer.lastname}${role}`;
                        interviewerSelect.appendChild(option);
                    });
                }
                
                interviewerSelect.disabled = false;
            } else {
                console.error('API returned success=false:', data);
                interviewerSelect.innerHTML = '<option value="">Error: ' + (data.message || 'Unknown error') + '</option>';
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            interviewerSelect.innerHTML = '<option value="">Error loading interviewers</option>';
        });
    });
    
    // Auto-fill email when interviewer is selected
    interviewerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const email = selectedOption.getAttribute('data-email');
        interviewerEmail.value = email || '';
    });

    // Submit status form normally when status changes
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endsection
