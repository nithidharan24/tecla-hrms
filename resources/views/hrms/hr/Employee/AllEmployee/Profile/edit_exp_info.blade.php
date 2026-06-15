@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Experience Information</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Experience Information</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Experience Information</h4>
                    <div class="card-body">
                        <form action="{{ route('employee.experience.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div id="experience_section_container">
                                <!-- Existing experience data -->
                                @foreach($experienceInfo as $index => $info)
                                    <div class="card mb-3 experience-card" data-id="{{ $info->id }}">
                                        <div class="card-body">
                                            <h4>Experience Information 
                                                <a href="javascript:void(0);" class="text-danger remove-experience d-none">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </h4>
                                            <div class="row">
                                                <input type="hidden" name="experience[{{ $index }}][id]" value="{{ $info->id }}">

                                                <div class="col-md-6 mb-3">
                                                    <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="experience[{{ $index }}][company_name]" class="form-control"
                                                           value="{{ old("experience.$index.company_name", $info->company_name) }}" required>
                                                </div>
                            
                                                <div class="col-md-6 mb-3">
                                                    <label class="col-form-label">Location <span class="text-danger">*</span></label>
                                                    <input type="text" name="experience[{{ $index }}][location]" class="form-control"
                                                           value="{{ old("experience.$index.location", $info->location) }}" required>
                                                </div>
                            
                                                <div class="col-md-6 mb-3">
                                                    <label class="col-form-label">Job Position <span class="text-danger">*</span></label>
                                                    <input type="text" name="experience[{{ $index }}][job_position]" class="form-control"
                                                           value="{{ old("experience.$index.job_position", $info->position) }}" required>
                                                </div>
<!-- Period From -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Period From <span class="text-danger">*</span></label>
    <input type="date" 
           name="experience[{{ $index }}][period_from]" 
           class="form-control"
           value="{{ old("experience.$index.period_from", $info->period_from ? \Carbon\Carbon::parse($info->period_from)->format('Y-m-d') : '') }}" 
           required>
</div>

<!-- Period To -->
<div class="col-md-6 mb-3">
    <label class="col-form-label">Period To <span class="text-danger">*</span></label>
    <input type="date" 
           name="experience[{{ $index }}][period_to]" 
           class="form-control"
           value="{{ old("experience.$index.period_to", $info->period_to ? \Carbon\Carbon::parse($info->period_to)->format('Y-m-d') : '') }}" 
           required>
</div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Add new experience button -->
                            <div class="d-flex justify-content-between">
    <button type="button" id="add_experience" class="btn btn-primary btn-lg">Add Experience</button>
    <button type="submit" class="btn btn-success btn-lg">Update</button>
</div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add_experience').onclick = function() {
        const container = document.getElementById('experience_section_container');
        const index = container.children.length;

        const newExperience = `
            <div class="card mb-3 experience-card">
                <div class="card-body">
                    <h4>New Experience Information
                        <a href="javascript:void(0);" class="text-danger remove-experience"><i class="fa fa-trash"></i></a>
                    </h4>
                    <div class="row">
                        <input type="hidden" name="experience[${index}][id]" value="">

                        <!-- Company Name -->
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="experience[${index}][company_name]" class="form-control" required>
                        </div>

                        <!-- Location -->
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" name="experience[${index}][location]" class="form-control" required>
                        </div>

                        <!-- Job Position -->
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Job Position <span class="text-danger">*</span></label>
                            <input type="text" name="experience[${index}][job_position]" class="form-control" required>
                        </div>

                        <!-- Period From -->
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Period From <span class="text-danger">*</span></label>
                            <input type="date" name="experience[${index}][period_from]" class="form-control" required>
                        </div>

                        <!-- Period To -->
                        <div class="col-md-6 mb-3">
                            <label class="col-form-label">Period To <span class="text-danger">*</span></label>
                            <input type="date" name="experience[${index}][period_to]" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newExperience);
        attachRemoveHandlers();  // Attach delete handler for newly added section
    };

    // Function to handle dynamic remove buttons
    function attachRemoveHandlers() {
        const deleteButtons = document.querySelectorAll('.remove-experience');
        deleteButtons.forEach(button => {
            button.onclick = function() {
                this.closest('.experience-card').remove();
            };
        });
    }

    // Attach remove handlers for pre-existing cards
    attachRemoveHandlers();
</script>
@endsection
