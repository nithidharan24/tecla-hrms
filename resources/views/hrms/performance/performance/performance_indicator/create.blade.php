@extends('layouts.index') 
@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Performance Indicator</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('performance-indicator.index')}}">Performance </a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('performance-indicator.create')}}">Add</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Form Start -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="card shadow-sm">
                <h4 class="card-header">Set New Indicator</h4>
                <div class="card-body">
                    <form id="addResignationForm" method="POST" action="{{ route('performance-indicator.store') }}">
                        @csrf
                        
                        <!-- Designation -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="designation">Designation<span class="text-danger">*</span></label>
                                <select class="form-control select2" name="designation" id="designation">
                                    <option value="" disabled selected>Select a Designation</option>
                                    @foreach ($designations as $designation)
                                        <option value="{{ $designation->id }}">{{ $designation->designation }}</option>
                                    @endforeach
                                </select>
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
                                @endphp
                                @foreach($technicalSkills as $skill)
                                    <div class="mb-4">
                                        <label for="{{ strtolower(str_replace(' ', '_', $skill)) }}">{{ $skill }}</label>
                                        <select class="form-control" name="technical[{{ strtolower(str_replace(' ', '_', $skill)) }}]">
                                            <option value="none">None</option>
                                            <option value="beginner">Beginner</option>
                                            <option value="intermediate">Intermediate</option>
                                            <option value="advanced">Advanced</option>
                                            <option value="expert">Expert</option>
                                        </select>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Organizational -->
                            <div class="col-md-6">
                                <h5><strong>Organizational</strong><span class="text-danger">*</span></h5>
                                @php
                                    $organizationalSkills = ['Integrity', 'Professionalism', 'Team Work', 'Critical Thinking', 'Conflict Management', 'Attendance', 'Ability To Meet Deadline'];
                                @endphp
                                @foreach($organizationalSkills as $skill)
                                    <div class="mb-4">
                                        <label for="{{ strtolower(str_replace(' ', '_', $skill)) }}">{{ $skill }}</label>
                                        <select class="form-control" name="organizational[{{ strtolower(str_replace(' ', '_', $skill)) }}]">
                                            <option value="none">None</option>
                                            <option value="beginner">Beginner</option>
                                            <option value="intermediate">Intermediate</option>
                                            <option value="advanced">Advanced</option>
                                            <option value="expert">Expert</option>
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
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                       <div class="row mt-4 text-center">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <button class="btn btn-primary" type="submit" id="submitBtn">Submit</button>
    </div>
</div>

<!-- jQuery Script -->
<script>
    $('#submitBtn').on('click', function () {
        $(this).prop('disabled', true).text('Processing...');
        $(this).closest('form').submit(); // Automatically submits the form
    });
</script>

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
                placeholder: "Select an Designation",
                allowClear: true
            });
        });
</script>
@endsection
