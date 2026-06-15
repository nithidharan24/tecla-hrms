{{-- resources/views/hrms/Employee/Schedule/edit.blade.php --}}
@extends('layouts.index')

@php
    $today = \Carbon\Carbon::today();
    $endDate = \Carbon\Carbon::parse($schedule->schedule_end_date);
    $daysUntilEnd = $today->diffInDays($endDate, false);
    $createNewSchedule = $daysUntilEnd <= 3 && $daysUntilEnd >= 0;
    $newStartDate = $endDate->copy()->addDay();
@endphp

@section('content')
<style>
:root{--lv-primary:#F97316;--lv-primary-dark:#EA580C}
.lv-wrap{background:#F5F6FA;min-height:100vh;padding:24px 32px;font-family:'DM Sans',sans-serif}
@media(max-width:767px){.lv-wrap{padding:12px 10px}}
.lv-topbar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:24px}
.lv-page-title{font-size:22px;font-weight:700;color:#0B1437;margin:0}
.lv-page-sub{font-size:13px;color:#8892B0;margin:2px 0 0}
.breadcrumb{background:transparent;padding:0;margin:0}
.lv-panel{background:#fff;border:1px solid #E5E7EB;border-radius:14px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.02)}
.lv-panel-header{padding:0 0 20px 0;margin:0 0 20px 0;border-bottom:1px solid #E5E7EB}
.lv-panel-title{font-size:16px;font-weight:700;color:#0B1437;margin:0}
.lv-input, .form-control{height:40px;border:1px solid #E5E7EB;border-radius:8px;padding:0 12px;font-size:13px;color:#374151;background:#fff;transition:border-color 0.2s}
.lv-input:focus, .form-control:focus{border-color:var(--lv-primary);box-shadow:0 0 0 3px rgba(249,115,22,0.1);outline:none}
.form-label, label{font-size:13px;font-weight:600;color:#4B5563;margin-bottom:6px}
.lv-btn{display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s}
.lv-btn-primary{background:var(--lv-primary);color:#fff}.lv-btn-primary:hover{background:var(--lv-primary-dark);color:#fff}
.lv-btn-secondary{background:#f3f4f6;color:#374151}.lv-btn-secondary:hover{background:#e5e7eb}
</style>

<div class="lv-wrap">
    <div class="lv-topbar">
        <div>
            <h1 class="lv-page-title">Edit Schedule</h1>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('scheduling.index') }}">Scheduling</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ul>
        </div>
    </div>

    @if ($createNewSchedule)
        <div class="alert alert-info mb-4">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Important Notice:</strong> You're editing a schedule that ends in {{ $daysUntilEnd + 1 }} day(s).
            This will create a <strong>new schedule</strong> starting from {{ $newStartDate->format('M d, Y') }}.
            The current schedule will remain active until {{ $endDate->format('M d, Y') }}.
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="lv-panel">
                <div class="lv-panel-header">
                    <h4 class="lv-panel-title">
                        @if ($createNewSchedule)
                            Create New Schedule (Effective {{ $newStartDate->format('M d, Y') }})
                        @else
                            Edit Current Schedule
                        @endif
                    </h4>
                </div>
                <div class="card-body">
                    <form id="editSchedule" method="POST" action="{{ route('scheduling.update', $schedule->id) }}">
                        @csrf
                        @method('PUT')

                        @if ($createNewSchedule)
                            <input type="hidden" name="create_new_schedule" value="1">
                            <input type="hidden" name="current_schedule_end_date" value="{{ $endDate->format('Y-m-d') }}">
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" name="department" required>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $schedule->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employee</label>
                                <select class="form-control" name="employee" required>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ $schedule->employee_id == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->firstname }} {{ $emp->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date"
                                       class="form-control"
                                       name="schedule_start_date"
                                       value="{{ $createNewSchedule ? $newStartDate->format('Y-m-d') : $schedule->schedule_start_date }}"
                                       min="{{ $createNewSchedule ? $newStartDate->format('Y-m-d') : $today->format('Y-m-d') }}"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date"
                                       class="form-control"
                                       name="schedule_end_date"
                                       value="{{ $schedule->schedule_end_date }}"
                                       min="{{ $createNewSchedule ? $newStartDate->format('Y-m-d') : $schedule->schedule_start_date }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Repeat Every Week</label>
                                <select class="form-control" name="repeat_every_week" id="repeatEveryWeekSelect" required>
                                    <option value="1" {{ $schedule->repeat_every_week ? 'selected' : '' }}>Yes - repeat every week</option>
                                    <option value="0" {{ !$schedule->repeat_every_week ? 'selected' : '' }}>No - one-time schedule</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Shift</label>
                                <select class="form-control" name="shift" id="shiftSelect" required>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            data-start="{{ $shift->start_time }}"
                                            data-end="{{ $shift->end_time }}"
                                            data-break="{{ $shift->break_time }}"
                                            data-days="{{ $shift->days_of_week }}"
                                            {{ $schedule->shift_id == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->shift_name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="start_time" id="startTime" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" id="endTime" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Break (minutes)</label>
                                <input type="number" class="form-control" name="break_time" id="breakTime" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Working Days</label>
                                <div id="daysContainer" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>

                        <small class="form-text text-warning">
                            ⚠️ Select dates that cover all days of the chosen shift (e.g., if shift is Mon–Fri, your dates must include Mon to Fri).
                        </small>

                        <div class="text-end mt-4">
                            <button type="submit" class="lv-btn lv-btn-primary">
                                @if ($createNewSchedule)
                                    Create New Schedule
                                @else
                                    Update Schedule
                                @endif
                            </button>
                            <a href="{{ route('scheduling.index') }}" class="lv-btn lv-btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shiftSelect = document.getElementById('shiftSelect');
        const startTime = document.getElementById('startTime');
        const endTime = document.getElementById('endTime');
        const breakTime = document.getElementById('breakTime');
        const daysContainer = document.getElementById('daysContainer');

        const daysMap = {
            'mon': 'Monday',
            'tue': 'Tuesday',
            'wed': 'Wednesday',
            'thu': 'Thursday',
            'fri': 'Friday',
            'sat': 'Saturday',
            'sun': 'Sunday'
        };

        function updateShiftFields() {
            const selectedOption = shiftSelect.options[shiftSelect.selectedIndex];
            startTime.value = selectedOption.dataset.start;
            endTime.value = selectedOption.dataset.end;
            breakTime.value = selectedOption.dataset.break;

            const days = selectedOption.dataset.days.toLowerCase().split(',');
            daysContainer.innerHTML = '';

            Object.entries(daysMap).forEach(([key, day]) => {
                const isChecked = days.includes(key);
                daysContainer.innerHTML += `
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox"
                            id="day${key}" value="${key}" ${isChecked ? 'checked' : ''} disabled>
                        <label class="form-check-label" for="day${key}">${day.substring(0,3)}</label>
                    </div>
                `;
            });
        }

        updateShiftFields();
        shiftSelect.addEventListener('change', updateShiftFields);

        const startDateInput = document.querySelector('input[name="schedule_start_date"]');
        const endDateInput = document.querySelector('input[name="schedule_end_date"]');

        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (new Date(endDateInput.value) < new Date(this.value)) {
                endDateInput.value = this.value;
            }
        });
    });
</script>
@endsection