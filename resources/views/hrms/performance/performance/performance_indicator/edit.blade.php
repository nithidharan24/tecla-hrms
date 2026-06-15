@extends('layouts.index') 
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Performance Indicator</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('performance-indicator.index')}}">Performance </a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Edit Indicator</h4>
                <div class="card-body">
                    <form id="editIndicatorForm" method="POST" action="{{ route('performance-indicator.update', $performance_indicators->id) }}">
                        @csrf
                        @method('PUT')
                        <!-- Designation -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="designation">Designation<span class="text-danger">*</span></label>
                                <select class="form-control select2" disabled>
                                    <option value="" disabled>Select a Designation</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}" {{ $performance_indicators->designation_id == $designation->id ? 'selected' : '' }}>
                                            {{ $designation->designation }}
                                        </option>
                                    @endforeach
                                </select>
                                <!-- Hidden Input to Submit the Selected Value -->
                                <input type="hidden" name="designation" value="{{ $performance_indicators->designation_id }}">

                                @if ($errors->has('designation'))
                                    <div class="text-danger mb-2">
                                        {{ $errors->first('designation') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <!-- Technical -->
                            <div class="col-md-6">
                                <h5><strong>Technical</strong><span class="text-danger">*</span></h5>
                                @php
                                    $technicalSkills = ['Customer Experience', 'Marketing', 'Management', 'Administration', 'Presentation Skill', 'Quality Of Work', 'Efficiency'];
                                    $existingTechnicalSkills = json_decode($performance_indicators->technical, true);
                                @endphp
                                @foreach($technicalSkills as $skill)
                                    <div class="mb-4">
                                        <label for="{{ strtolower(str_replace(' ', '_', $skill)) }}">{{ $skill }}</label>
                                        <select class="form-control" name="technical[{{ strtolower(str_replace(' ', '_', $skill)) }}]">
                                            <option value="none" {{ $existingTechnicalSkills[strtolower(str_replace(' ', '_', $skill))] == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="beginner" {{ $existingTechnicalSkills[strtolower(str_replace(' ', '_', $skill))] == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                            <option value="intermediate" {{ $existingTechnicalSkills[strtolower(str_replace(' ', '_', $skill))] == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                            <option value="advanced" {{ $existingTechnicalSkills[strtolower(str_replace(' ', '_', $skill))] == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            <option value="expert" {{ $existingTechnicalSkills[strtolower(str_replace(' ', '_', $skill))] == 'expert' ? 'selected' : '' }}>Expert</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Organizational -->
                            <div class="col-md-6">
                                <h5><strong>Organizational</strong><span class="text-danger">*</span></h5>
                                @php
                                    $organizationalSkills = ['Integrity', 'Professionalism', 'Team Work', 'Critical Thinking', 'Conflict Management', 'Attendance', 'Ability To Meet Deadline'];
                                    $existingOrganizationalSkills = json_decode($performance_indicators->organizational, true);
                                @endphp
                                @foreach($organizationalSkills as $skill)
                                    <div class="mb-4">
                                        <label for="{{ strtolower(str_replace(' ', '_', $skill)) }}">{{ $skill }}</label>
                                        <select class="form-control" name="organizational[{{ strtolower(str_replace(' ', '_', $skill)) }}]">
                                            <option value="none" {{ $existingOrganizationalSkills[strtolower(str_replace(' ', '_', $skill))] == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="beginner" {{ $existingOrganizationalSkills[strtolower(str_replace(' ', '_', $skill))] == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                            <option value="intermediate" {{ $existingOrganizationalSkills[strtolower(str_replace(' ', '_', $skill))] == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                            <option value="advanced" {{ $existingOrganizationalSkills[strtolower(str_replace(' ', '_', $skill))] == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                            <option value="expert" {{ $existingOrganizationalSkills[strtolower(str_replace(' ', '_', $skill))] == 'expert' ? 'selected' : '' }}>Expert</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="status">Status<span class="text-danger">*</span></label>
                                <select class="form-control" name="status" id="status">
                                    <option value="active" {{ $performance_indicators->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $performance_indicators->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4 text-center">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Form End -->
</div>
<!-- /Page Content -->

<script>
    $(document).ready(function() {
        // Initialize select2 with placeholder
        $('#designation').select2({
            placeholder: "Select a Designation",
            allowClear: true
        });
    });
</script>

@endsection
