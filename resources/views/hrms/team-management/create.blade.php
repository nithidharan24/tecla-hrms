@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container mt-5">
        <div class="page-header">
            <h2 class="pageheader-title">Team Management</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Team management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Create New Team</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('team.store') }}" method="POST">
                    @csrf
                    
                    <!-- Team Basic Information -->
                    <div class="form-group mb-4">
                        <label for="team_name">Team Name</label>
                        <input type="text" class="form-control" id="team_name" name="team_name" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="team_description">Description</label>
                        <textarea class="form-control" id="team_description" name="team_description" rows="3"></textarea>
                    </div>
                    
                    <!-- Manager Selection -->
                    <div class="form-group mb-4">
                        <label for="manager_id">Manager</label>
                        <select class="form-control select2" id="manager_id" name="manager_id" required>
                            <option value="">Select Manager</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->firstname }}</option>
                        @endforeach
                        </select>
                    </div>
                    
                  <!-- Team Leader Selection -->
<div class="form-group mb-4">
    <label for="team_leader_id">Team Leader</label>
    <select class="form-control" id="team_leader_id" name="team_leader_id" required>
        <option value="">Select Team Leader</option>
        @foreach($employees as $employee)
        <option value="{{ $employee->id }}">{{ $employee->firstname }}</option>
    @endforeach
    </select>
</div>
                    
<!-- Employees Selection -->
<div class="form-group mb-4">
    <label for="employees">Team Members</label>
    <div id="employee-fields-container">
        <div class="employee-field-group mb-2 d-flex align-items-center">
          
            <select class="form-control select2-employee" name="employees[]" required style="flex-grow: 1;">
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->firstname }}</option>
                @endforeach
            </select>
            <span class="mr-2">-</span>
            <button type="button" class="btn btn-success btn-sm ml-2 add-employee-btn" style="width: 38px; height: 38px;">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</div>
                    
                  <!-- Projects Assignment -->
                  <div class="form-group mb-4">
                    <label for="project_id">Assigned Project</label>
                    <select class="form-control" id="project_id" name="project_id" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->projectname }}</option>
                        @endforeach
                    </select>
                </div>
                    
                    <div class="form-group mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">Create Team</button>
                        <a href="{{ route('team.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();
        $('.select2-multiple').select2();
    });



    $(document).ready(function() {
        // Initialize Select2 for employee fields
        $('.select2-employee').select2();
        
        // Add new employee field
        $(document).on('click', '.add-employee-btn', function() {
            var $clone = $('.employee-field-group:first').clone();
            $clone.find('select').val('').select2();
            $clone.find('.add-employee-btn')
                .removeClass('btn-success add-employee-btn')
                .addClass('btn-danger remove-employee-btn')
                .html('<i class="fas fa-minus"></i>');
            $('#employee-fields-container').append($clone);
            $clone.find('.select2-employee').select2();
        });
        
        // Remove employee field
        $(document).on('click', '.remove-employee-btn', function() {
            if($('.employee-field-group').length > 1) {
                $(this).closest('.employee-field-group').remove();
            }
        });
    });
</script>



<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .card {
        margin-bottom: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        padding: 15px 20px;
    }
    .card-body {
        padding: 30px;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .btn {
        padding: 8px 20px;
    }

    .employee-field-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .select2-employee {
        min-width: 200px;
    }
    .add-employee-btn, .remove-employee-btn {
        flex-shrink: 0;
    }
</style>

@endsection