@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Add Overtime</h4>
                <div class="card-body">
                    <form id="overtimeForm" action="{{ isset($overtime) ? route('payovertime.update', $overtime->id) : route('payovertime.store') }}" method="POST">
                        @csrf
                        @if(isset($overtime))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <!-- Name Input -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="col-form-label">Name <span class="text-danger">*</span></label>
                                <input class="form-control" id="name" name="name" type="text" 
                                       value="{{ old('name', $overtime->name ?? '') }}" required>
                                <small class="text-danger" id="name-error" style="display:none;"></small>
                            </div>

                            <!-- Hierarchy Dropdown -->
                            <div class="col-md-6 mb-3">
                                <label for="hierarchy_id" class="col-form-label">Hierarchy <span class="text-danger">*</span></label>
                                <select class="form-select" id="hierarchy_id" name="hierarchy_id" required>
                                    <option value="">Select Hierarchy</option>
                                    @foreach($hierarchies as $hierarchy)
                                        <option value="{{ $hierarchy->id }}" 
                                            {{ (old('hierarchy_id', $overtime->hierarchy_id ?? '') == $hierarchy->id) ? 'selected' : '' }}>
                                            {{ $hierarchy->hierarchy_level }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="hierarchy_id-error" style="display:none;"></small>
                            </div>

                            <!-- Rate Input -->
                            <div class="col-md-6 mb-3">
                                <label for="rate" class="col-form-label">Rate (Per Hour) <span class="text-danger">*</span></label>
                                <input class="form-control" id="rate" name="rate" type="number" step="0.01" min="0" 
                                       value="{{ old('rate', $overtime->rate ?? '') }}" required>
                                <small class="text-muted">Enter the hourly rate for overtime</small>
                                <small class="text-danger" id="rate-error" style="display:none;"></small>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-md-12 mt-4">
                                <button class="btn btn-primary btn-lg btn-block" type="submit" id="submitBtn">
                                    {{ isset($overtime) ? 'Update' : 'Save' }} Overtime
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation + Processing Script -->
<script>
    // Hierarchy validation
    document.getElementById('hierarchy_id').addEventListener('change', function() {
        const error = document.getElementById('hierarchy_id-error');
        if (this.value === '') {
            error.textContent = 'Hierarchy selection is required.';
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });

    // Prevent multiple submission & show processing state
    const form = document.getElementById("overtimeForm");
    const submitBtn = document.getElementById("submitBtn");

    form.addEventListener("submit", function() {
        if (form.checkValidity()) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
        }
    });
</script>
@endsection
