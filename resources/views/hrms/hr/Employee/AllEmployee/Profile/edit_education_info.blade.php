@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Edit Education Information</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="" class="breadcrumb-link">Employee List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Education Information</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Edit Education Information</h4>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{ route('education.update', $employee->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div id="education_section_container">
                                <!-- Existing education data -->
                                @foreach($educationInfos as $index => $education)
                                <div class="card mb-3 education-card" data-id="{{ $education->id }}">
                                    <div class="card-body">
                                        <h4>Education Information 
                                            <a href="javascript:void(0);" class="text-danger remove-education d-none"><i class="fa fa-trash"></i></a>
                                        </h4>
                                        <div class="row">
                                            <input type="hidden" name="education[{{ $index }}][id]" value="{{ $education->id }}">

                                            <div class="col-md-6 mb-3">
                                                <label>Institution <span class="text-danger">*</span></label>
                                                <input type="text" name="education[{{ $index }}][institution]" class="form-control" value="{{ $education->institution }}" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Subject <span class="text-danger">*</span></label>
                                                <input type="text" name="education[{{ $index }}][subject]" class="form-control" value="{{ $education->subject }}" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Starting Date</label>
                                                <input type="date" name="education[{{ $index }}][start_date]" 
                                                       class="form-control" 
                                                       value="{{ $education->start_date ? \Carbon\Carbon::parse($education->start_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label>Completion Date</label>
                                                <input type="date" name="education[{{ $index }}][end_date]" 
                                                       class="form-control" 
                                                       value="{{ $education->end_date ? \Carbon\Carbon::parse($education->end_date)->format('Y-m-d') : '' }}">
                                            </div>
                                                

                                            <div class="col-md-6 mb-3">
                                                <label>Degree</label>
                                                <input type="text" name="education[{{ $index }}][degree]" class="form-control" value="{{ $education->degree }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label>Grade</label>
                                                <input type="text" name="education[{{ $index }}][grade]" class="form-control" value="{{ $education->grade }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Add new education button -->
                            <div class="d-flex justify-content-between">
    <button type="button" id="add_education" class="btn btn-primary btn-lg">Add Education</button>
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
    document.getElementById('add_education').onclick = function() {
        const container = document.getElementById('education_section_container');
        const index = container.children.length;

        const newEducation = `
            <div class="card mb-3 education-card">
                <div class="card-body">
                    <h4>New Education Information
                        <a href="javascript:void(0);" class="text-danger remove-education"><i class="fa fa-trash"></i></a>
                    </h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Institution <span class="text-danger">*</span></label>
                            <input type="text" name="education[${index}][institution]" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Subject <span class="text-danger">*</span></label>
                            <input type="text" name="education[${index}][subject]" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Starting Date</label>
                            <input type="text" name="education[${index}][start_date]" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Completion Date</label>
                            <input type="text" name="education[${index}][end_date]" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Degree</label>
                            <input type="text" name="education[${index}][degree]" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Grade</label>
                            <input type="text" name="education[${index}][grade]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newEducation);
        attachRemoveHandlers();  // Attach delete handler for newly added section
    };

    // Function to handle dynamic remove buttons
    function attachRemoveHandlers() {
        const deleteButtons = document.querySelectorAll('.remove-education');
        deleteButtons.forEach(button => {
            button.onclick = function() {
                this.closest('.education-card').remove();
            };
        });
    }

    // Attach remove handlers for pre-existing cards
    attachRemoveHandlers();
</script>
@endsection
