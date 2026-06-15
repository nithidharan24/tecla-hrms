@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit GoalTrack</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('show-goaltrack')}}">Goal Track</a></li>
                    <li class="breadcrumb-item active">Edit GoalTrack</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

</div>
<!-- /Page Content -->

<!-- Edit Goal Modal -->
<div class="container-fluid mt-4">
    <div class="container row">
        <h5 class="mb-4">Edit Goal Tracking</h5>
        <form id="editgoalTrack" method="POST" action="{{ route('update-goaltrack', $goalTrack->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-sm-12">
                    <label class="focus-label">Goal Type <span class="text-danger">*</span></label>
                    <div class="input-block mb-3 form-focus select-focus">
                        <select id="goalType" name="goal" class="select floating"> 
                            <option value="">Select Goal Type</option>
                            @foreach ($goalTypes as $item)
                            <option value="{{$item->id}}" {{$item->id == $goalTrack->goal ? 'selected':''}}>{{ucFirst($item->goal)}}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="goalSubject" class="col-form-label">Subject <span class="text-danger">*</span></label>
                        <input id="goalSubject" name="subject" class="form-control" type="text" value="{{ old('subject', $goalTrack->subject) }}" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="targetAchievement" class="col-form-label">Target Achievement <span class="text-danger">*</span></label>
                        <input id="targetAchievement" name="achievement" class="form-control" type="text" value="{{ old('achievement', $goalTrack->achievement) }}" />
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="startDate" class="col-form-label">Start Date <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input id="startDate" name="sdate" class="form-control datetimepicker" type="text" 
                                   value="{{ \Carbon\Carbon::parse($goalTrack->start_date)->format('d-m-Y') }}" placeholder="dd-mm-yyyy" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-block mb-3">
                        <label for="endDate" class="col-form-label">End Date <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input id="endDate" name="edate" class="form-control datetimepicker" type="text" 
                                   value="{{ \Carbon\Carbon::parse($goalTrack->end_date)->format('d-m-Y') }}" placeholder="dd-mm-yyyy"/>
                        </div>
                    </div>
                </div>                

                <div class="col-sm-12 mb-3">
                    <div class="input-block mb-3">
                        <label for="customRange">Progress</label>
                        <input type="range" class="form-control-range form-range" id="customRange" name="progress" value="{{ old('progress', $goalTrack->progress) }}">
                        <div class="mt-2" id="result">Progress Value: <b></b></div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="input-block mb-3">
                        <label for="goalDescription" class="col-form-label">Description <span class="text-danger">*</span></label>
                        <textarea id="goalDescription" name="description" style="resize: none;" class="form-control" rows="4">{{ old('description', $goalTrack->description) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="submit-section my-3 ">
                <button type="submit" class="btn btn-primary submit-btn">update</button>
            </div>
        </form>
    </div>
</div>
<!-- /Edit Goal Modal -->

<script>

    // Jquery Validation
    $(document).ready(function () {
        // Custom validation method for "First Name"
        $.validator.addMethod("customName", function (value, element) {
            return this.optional(element) || /^[^\s].{0,29}$/.test(value);
        }, "Invalid name format. No leading spaces allowed, and must not exceed 30 characters.");

        $("#result b").html($("#customRange").val());

        // Read value on change
        $("#customRange").change(function(){
            $("#result b").html($(this).val());
        });

        // Form validation
        $('#editgoalTrack').validate({
            rules: {
                goal: {
                    required: true
                },
                subject: {
                    required: true,
                    minlength: 3 
                },
                achievement: {
                    required: true,
                    minlength: 3,
                    maxlength: 255,
                },
                sdate: {
                    required: true,
                },
                edate: {
                    required: true,
                },
                description: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                }
            },
            messages: {
                goal: {
                    required: "Please select a goal type."
                },
                subject: {
                    required: "Please enter a subject.",
                    minlength: "Subject must be at least 3 characters long."
                },
                achievement: {
                    required: "Please enter a target achievement.",
                    minlength: "Achievement must be at least 3 characters long.",
                    maxlength: "Achievement does not exceed 255 characters."
                },
                sdate: {
                    required: "Please enter a start date.",
                },
                edate: {
                    required: "Please enter an end date.",
                },
                description: {
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
                form.submit();
            }
        });


    });

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