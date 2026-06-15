@extends('layouts.index')
@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <div class="container mt-5">
        <div class="page-header">
            <h2 class="pageheader-title">Create Project</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}" class="breadcrumb-link">Project List</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
        <form action="{{ route('projects.store') }}" method="POST" class="shadow p-4 rounded" enctype="multipart/form-data" id="addproject">
            @csrf
            <h3 class="mb-4">Add New Project</h3>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="projectname" class="form-label">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="projectname" name="projectname" required>
                </div>
                <div class="col-md-6">
                    <label for="client">Client <span class="text-danger">*</span></label>
                    <select class="form-control select2" name="client" id="client">
                        <option value="" disabled selected>Select an client</option>
                        @foreach ($client as $cl)
                    <option value="{{ $cl->client_id }}">{{ $cl->company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="startdate" class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="startdate" name="startdate" required>
            </div>
            <div class="col-md-6">
                <label for="enddate" class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="enddate" name="enddate" required>
                <div class="invalid-feedback" id="date-error">End date must be after the start date.</div>
            </div>
        </div>
            <!-- <div class="row mb-3">
                <div class="col-md-6">
                    <label for="rate" class="form-label">Rate <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="" id="rate" name="rate" required>
                </div>
                <div class="col-md-6">
                    <label for="worktype" class="form-label">Work Type <span class="text-danger">*</span></label>
                    <select class="form-control" id="worktype" name="worktype" required>
                        <option value="">Select work type...</option>
                        <option value="Hourly">Hourly</option>
                        <option value="Fixed">Fixed</option>
                    </select>
                </div>
            </div> -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="projectleader" class="form-label">Project Leader <span class="text-danger">*</span></label>
                    <select class="form-control select2-employee" id="projectleader" name="projectleader" required>
                        <option value="" disabled selected>Select Project Leader</option>
                        @foreach($employees as $employee)
                              <option value="{{ $employee->id }}" {{ isset($currentLeaderId) && $currentLeaderId == $employee->id ? 'selected' : '' }}>
                                {{ $employee->firstname }} {{ $employee->lastname }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="team" class="form-label">Team <span class="text-danger">*</span></label>
                    <select class="form-control select2-employee" id="team" name="team[]" multiple>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" >{{ $employee->firstname }} {{ $employee->lastname }}</option>
                        @endforeach
                    </select>
                    <!-- Display Team Member Images -->
                    <div id="teamImages" class="mt-2"></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="">Select priority...</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
             
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="projectfile" class="form-label">Upload Project File</label>
                <input type="file" class="form-control" id="projectfile" name="projectfile">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
              <a href="{{ route('projects.index') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
        </form>
    </div>
</div>
<script>
    $(document).ready(function ($) {
        // <CHANGE> Store original team options for filtering
        var originalTeamOptions = $('#team option').clone();
        
        // <CHANGE> Function to filter team dropdown based on selected project leader
        function filterTeamDropdown() {
            var selectedLeaderId = $('#projectleader').val();
            
            // Clear current team options
            $('#team').empty();
            
           // Add all team options (including selected leader)
originalTeamOptions.each(function() {
    $('#team').append($(this).clone());
});

            // Remove selected leader from team if already selected
            if (selectedLeaderId) {
                $('#team').val($('#team').val().filter(function(value) {
                    return value !== selectedLeaderId;
                }));
            }
            
            // Trigger change to update Select2 display
            $('#team').trigger('change');
        }
        
        // <CHANGE> Listen for project leader changes
        $('#projectleader').on('change', function() {
            filterTeamDropdown();
        });
        
        // <CHANGE> Initial filter on page load
        filterTeamDropdown();

        // Custom validation for text inputs
        $.validator.addMethod("customText", function(value, element) {
            return this.optional(element) || /^[^\s].{0,24}$/.test(value);
        }, "No leading spaces allowed, and the text must not exceed 25 characters.");
        // Update the validation rules for the form
        $("#addproject").validate({
            rules: {
                projectname: {
                    required: true,
                    customText: true
                },
                client: {
                    required: true,
                },
                startdate: {
                    required: true,
                },
                enddate: {
                    required: true,
                },
                rate: {
                    required: true,
                },
                projectleader: {
                    required: true,
                    // customText: true // Removed as it's a select now
                },
                "team[]": { // Changed from 'team' to 'team[]' for multiple select
                    required: true,
                },
                worktype: {
                    required: true,
                },
                priority: {
                    required: true,
                },
                status: {
                    required: true,
                },
                description: {
                    required: true,
                }
            },
            messages: {
                projectname: {
                    required: "Please enter a name",
                    customText: "No leading spaces allowed."
                },
                client: {
                    required: "Please select a client",
                },
                startdate: {
                    required: "Please enter a start date",
                },
                enddate: {
                    required: "Please enter an end date",
                },
                rate: {
                    required: "Please enter a rate",
                },
                worktype: {
                    required: "Please select a work type",
                },
                projectleader: {
                    required: "Please select a leader",
                    // customText: "No leading spaces allowed" // Removed
                },
                "team[]": { // Changed from 'team' to 'team[]'
                    required: "Please select team members",
                },
                priority: {
                    required: "Please select a priority",
                },
                status: {
                    required: "Please select a status",
                },
                description: {
                    required: "Please enter a description"
                }
            },
            errorClass: "error",
            errorPlacement: function(error, element) {
                if (element.is("select")) {
                    error.appendTo(element.parent()); // Place error message in parent div for select elements
                } else {
                    error.insertAfter(element); // Default behavior for other inputs
                }
            },
            highlight: function(element) {
                // Highlight only text inputs for error indication
                if (!$(element).is("select")) {
                    $(element).addClass("error");
                }
            },
            unhighlight: function(element) {
                // Remove error class only for text inputs
                if (!$(element).is("select")) {
                    $(element).removeClass("error");
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
        // Initialize CKEditor for the description textarea
        ClassicEditor
            .create(document.querySelector('#description'), {
                // Set the height of the editor
                height: '100px', // Adjust height as needed
            })
            .catch(error => {
                console.error(error);
            });
        // Initialize select2 with placeholder
        $('#client').select2({
            placeholder: "Select an client",
            allowClear: true
        });
    });
</script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for team with custom templates
        $('#team').select2({
            placeholder: "Select Team Members",
            templateResult: formatEmployeeOption,
            templateSelection: formatEmployeeSelection,
            tags: false,
            escapeMarkup: function(markup) { return markup; }
        });
        // Update Team Member images
        $('#team').on('change', function() {
            $('#teamImages').html(''); // Clear previous images
            $(this).find(':selected').each(function() {
                const imgUrl = $(this).data('image');
                if (imgUrl) {
                    const img = $('<img>', {
                        src: imgUrl,
                        style: 'width: 50px; height: 50px; border-radius: 50%; margin-right: 5px;',
                    });
                    $('#teamImages').append(img);
                }
            });
        });
        // Trigger team image update on page load if any selections exist
        $('#team').trigger('change');
    });
    // Reuse the same format functions from your existing code
    function formatEmployeeOption(option) {
        if (!option.id) {
            return option.text;
        }
        const imgUrl = $(option.element).data('image');
        const img = $('<img>', {
            src: imgUrl,
            style: 'width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;',
        });
        const span = $('<span>').text(option.text);
        return $('<span>').append(img).append(span);
    }
    function formatEmployeeSelection(option) {
        if (!option.id) {
            return option.text;
        }
        const imgUrl = $(option.element).data('image');
        const img = $('<img>', {
            src: imgUrl,
            style: 'width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;',
        });
        const span = $('<span>').text(option.text);
        return $('<span>').append(img).append(span);
    }
    // Initialize select2 for project leader
    $('#projectleader').select2({
        placeholder: "Select Project Leader",
        templateResult: formatEmployeeOption,
        templateSelection: formatEmployeeSelection,
        tags: false,
        escapeMarkup: function(markup) { return markup; }
    });
    const startDate = document.getElementById('startdate');
    const endDate = document.getElementById('enddate');
    const dateError = document.getElementById('date-error');
    function validateDates() {
        const start = new Date(startDate.value);
        const end = new Date(endDate.value);
        if (end <= start) {
            endDate.setCustomValidity("End date must be after start date");
            dateError.style.display = 'block';
        } else {
            endDate.setCustomValidity("");
            dateError.style.display = 'none';
        }
    }
    startDate.addEventListener('change', validateDates);
    endDate.addEventListener('change', validateDates);
</script>
@endsection
