@extends('layouts.index')
@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Edit Shift</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('shift.index') }}">Shifts</a></li>
                    <li class="breadcrumb-item active">Edit Shift</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('shift.update', $shift->id) }}" method="POST" id="shiftForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Shift Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="shift_name" value="{{ $shift->shift_name }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="start_time" 
                                           value="{{ date('H:i', strtotime($shift->start_time)) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Time <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="end_time" 
                                           value="{{ date('H:i', strtotime($shift->end_time)) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Break Time (minutes)</label>
                                    <input type="number" class="form-control" name="break_time" 
                                           value="{{ $shift->break_time }}" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Days of Week <span class="text-danger">*</span></label><br>
                                    @foreach(['Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 
                                             'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 
                                             'Sun' => 'Sunday'] as $value => $day)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" 
                                               name="days_of_week[]" 
                                               value="{{ $value }}" 
                                               id="{{ strtolower($value) }}"
                                               @if(in_array($value, $shift->days_of_week)) checked @endif>
                                        <label class="form-check-label" for="{{ strtolower($value) }}">{{ $day }}</label>
                                    </div>
                                    @endforeach
                                    <span id="daysError" class="text-danger"></span>
                                </div>
                            </div>
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Same validation as create form
    $('#shiftForm').validate({
        rules: {
            shift_name: {
                required: true,
                maxlength: 25
            },
            start_time: {
                required: true
            },
            end_time: {
                required: true,
                greaterThanStart: "#start_time"
            },
            break_time: {
                min: 0
            },
            'days_of_week[]': {
                required: true,
                minlength: 1
            }
        },
        messages: {
            shift_name: {
                required: "Please enter shift name",
                maxlength: "Shift name cannot exceed 25 characters"
            },
            start_time: {
                required: "Please select start time"
            },
            end_time: {
                required: "Please select end time",
                greaterThanStart: "End time must be after start time"
            },
            break_time: {
                min: "Break time cannot be negative"
            },
            'days_of_week[]': {
                required: "Please select at least one day",
                minlength: "Please select at least one day"
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr('name') == 'days_of_week[]') {
                error.appendTo('#daysError');
            } else {
                error.insertAfter(element);
            }
        }
    });

    $.validator.addMethod("greaterThanStart", function(value, element, param) {
        var startTime = $(param).val();
        if (!value || !startTime) return true;
        return new Date('2000-01-01 ' + value) > new Date('2000-01-01 ' + startTime);
    });
});
</script>
@endsection