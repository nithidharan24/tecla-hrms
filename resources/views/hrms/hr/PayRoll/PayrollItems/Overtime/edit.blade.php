@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Overtime</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('payroll.index') }}" class="breadcrumb-link">Overtime List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Overtime</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Overtime Entry</h4>
                    <div class="card-body">
                        <form action="{{ route('payovertime.update', $overtime->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Name Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="col-form-label">Name <span class="text-danger">*</span></label>
                                    <input class="form-control" id="name" name="name" type="text" 
                                           value="{{ old('name', $overtime->name) }}" required>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Hierarchy Dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label for="hierarchy_id" class="col-form-label">Hierarchy Level <span class="text-danger">*</span></label>
                                    <select class="form-select @error('hierarchy_id') is-invalid @enderror" id="hierarchy_id" name="hierarchy_id" required>
                                        <option value="">Select Hierarchy Level</option>
                                        @foreach($hierarchies as $hierarchy)
                                            <option value="{{ $hierarchy->id }}" 
                                                {{ old('hierarchy_id', $overtime->hierarchy_id) == $hierarchy->id ? 'selected' : '' }}>
                                                {{ $hierarchy->hierarchy_level }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hierarchy_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Rate Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="rate" class="col-form-label">Hourly Rate <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" 
                                               type="number" step="0.01" min="0" value="{{ old('rate', $overtime->rate) }}" required>
                                    </div>
                                    <small class="text-muted">Enter the hourly rate for overtime</small>
                                    @error('rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary me-2" type="submit">
                                        <i class="fas fa-save me-1"></i> Update Overtime
                                    </button>
                                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
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

<!-- Real-time validation script -->
<script>
    // Real-time validation for Name field
    document.getElementById('name').addEventListener('input', function() {
        const errorElement = this.nextElementSibling;
        if (this.value.trim() === '') {
            if (!errorElement || !errorElement.classList.contains('text-danger')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger';
                errorDiv.textContent = 'Name is required.';
                this.parentNode.insertBefore(errorDiv, this.nextSibling);
            }
        } else if (errorElement && errorElement.classList.contains('text-danger')) {
            errorElement.remove();
        }
    });

    // Real-time validation for Hierarchy selection
    document.getElementById('hierarchy_id').addEventListener('change', function() {
        const errorElement = this.nextElementSibling;
        if (this.value === '') {
            if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Please select a hierarchy level.';
                this.classList.add('is-invalid');
                this.parentNode.insertBefore(errorDiv, this.nextSibling);
            }
        } else if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            this.classList.remove('is-invalid');
            errorElement.remove();
        }
    });

    // Real-time validation for Rate field
    document.getElementById('rate').addEventListener('input', function() {
        const errorElement = this.nextElementSibling;
        if (this.value.trim() === '') {
            if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Rate is required.';
                this.classList.add('is-invalid');
                this.parentNode.insertBefore(errorDiv, this.nextSibling);
            }
        } else if (isNaN(this.value) || parseFloat(this.value) < 0) {
            if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Please enter a valid positive number.';
                this.classList.add('is-invalid');
                this.parentNode.insertBefore(errorDiv, this.nextSibling);
            }
        } else if (errorElement && errorElement.classList.contains('invalid-feedback')) {
            this.classList.remove('is-invalid');
            errorElement.remove();
        }
    });
</script>
@endsection