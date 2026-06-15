@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add GoalTrack</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('show-goaltrack')}}">Goal Track</a></li>
                    <li class="breadcrumb-item active">Add GoalTrack</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

</div>
<!-- /Page Content -->

<!-- Add Goal Track -->
<div class="container-fluid mt-4">
    <div class="container row">
        <h5 class="mb-4">Add Goal Tracking</h5>
        <form id="addgoalTrack" method="POST" action="{{route('store-goaltrack')}}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-12">
                    <label class="focus-label">Goal Type <span class="text-danger">*</span></label>
                    <div class="input-block mb-3 form-focus select-focus">
                        <select id="goalType" name="goal" class="select floating"> 
                            <option value="">Select Goal Type</option>
                            @foreach ($goaltypes as $item)
                            <option value="{{$item->id}}">{{ucFirst($item->goal)}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="goalSubject" class="col-form-label">Subject <span class="text-danger">*</span></label>
                        <input id="goalSubject" name="Subject" class="form-control" type="text" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="targetAchievement" class="col-form-label">Target Achievement <span class="text-danger">*</span></label>
                        <input id="targetAchievement" name="Achievement" class="form-control" type="text" />
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="startDate" class="col-form-label">Start Date <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input id="startDate" name="sDate" class="form-control datetimepicker" type="text" />
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="endDate" class="col-form-label">End Date <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input id="endDate" name="eDate" class="form-control datetimepicker" type="text" />
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="input-block mb-3">
                        <label for="goalDescription" class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea id="goalDescription" name="Description" style="resize: none;" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="submit-section my-3">
    <button type="submit" class="btn btn-primary submit-btn" id="submitBtn">Submit</button>
</div>

<!-- Include this script at the bottom of your page or inside $(document).ready() -->
<script>
    $('#submitBtn').on('click', function () {
        $(this).prop('disabled', true).text('Processing...');
        $(this).closest('form').submit(); // Submits the form
    });
</script>

        </form>
    </div>
</div>

<!-- /Add Goal Modal -->

<script>
    $(document).ready(function () {
        // Initialize date pickers
        $('.datetimepicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        // Custom validation method for end date
        $.validator.addMethod("endDateAfterStart", function(value, element) {
            var startDate = $('#startDate').val();
            if (!startDate || !value) return true;
            
            // Convert dates to Date objects for comparison
            var startParts = startDate.split("-");
            var endParts = value.split("-");
            
            var start = new Date(startParts[2], startParts[1] - 1, startParts[0]);
            var end = new Date(endParts[2], endParts[1] - 1, endParts[0]);
            
            return end > start;
        }, "End date must be after start date");

        $('#addgoalTrack').validate({
            rules: {
                goal: {
                    required: true
                },
                Subject: {
                    required: true,
                    minlength: 3 
                },
                Achievement: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                sDate: {
                    required: true,
                },
                eDate: {
                    required: true,
                    endDateAfterStart: true
                },
                Description: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                }
            },
            messages: {
                goal: {
                    required: "Please select a goal type."
                },
                Subject: {
                    required: "Please enter a subject.",
                    minlength: "Subject must be at least 3 characters long."
                },
                Achievement: {
                    required: "Please enter a target achievement.",
                    minlength: "Achievement must be at least 3 characters long.",
                    maxlength: "Achievement does not exceed 255 characters."
                },
                sDate: {
                    required: "Please enter a start date.",
                },
                eDate: {
                    required: "Please enter an end date.",
                },
                Description: {
                    required: "Please enter a description.",
                    minlength: "Description must be at least 10 characters long.",
                    maxlength: "Description does not exceed 500 characters."
                }
            },
            errorClass: "error",
            errorPlacement: function (error, element) {
                if (element.attr("name") === "goal") {
                    error.insertAfter(element.next());
                } else {
                    error.insertAfter(element);
                }
            },
            
            submitHandler: function(form) {
                $('#submitBtn').prop('disabled', true).text('Processing...');
                form.submit();
            }
        });

        // Optionally, you can also restrict the end date picker to only allow dates after start date
        $('#startDate').on('dp.change', function(e) {
            if ($('#startDate').val()) {
                var startParts = $('#startDate').val().split("-");
                var startDate = new Date(startParts[2], startParts[1] - 1, startParts[0]);
                startDate.setDate(startDate.getDate() + 1); // Add 1 day to start date
                
                $('#endDate').data("DateTimePicker").minDate(startDate);
                if ($('#endDate').val()) {
                    var endParts = $('#endDate').val().split("-");
                    var endDate = new Date(endParts[2], endParts[1] - 1, endParts[0]);
                    if (endDate <= startDate) {
                        $('#endDate').val('');
                    }
                }
            }
        });
    });

    //Show Backend Validation Error
    @if(Session::has('messageType') && Session::has('message'))
        Swal.fire({
            icon: "{{ Session::get('messageType') }}",
            title: "{{ Session::get('message') }}",
            showConfirmButton: true,
        });
    @endif

    // Show validation errors using SweetAlert
    @if ($errors->any())
    var errorMessage = '';
    @foreach ($errors->all() as $error)
        errorMessage += "{{ $error }}\n";
    @endforeach
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        text: errorMessage,
    });
    @endif
</script>






@endsection