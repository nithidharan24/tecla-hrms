@extends('layouts.index')

@section('content')

<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Schedule</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Schedule</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Schedule Timing</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('schedule.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="candidate_id" class="form-label">Candidate Name</label>
                            <select class="form-select" id="name" name="name" required>
                                <option value="" disabled selected>Select Candidate</option>
                                @foreach($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->first_name }} {{ $candidate->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="selectdate1" class="form-label">Select Date 1</label>
                            <input type="date" class="form-control" id="selectdate1" name="selectdate1" required>
                        </div>
                        <div class="mb-3">
                            <label for="selecttime1" class="form-label">Select Time 1</label>
                            <input type="time" class="form-control" id="selecttime1" name="selecttime1" required>
                        </div>

                        <div class="mb-3">
                            <label for="selectdate2" class="form-label">Select Date 2</label>
                            <input type="date" class="form-control" id="selectdate2" name="selectdate2">
                        </div>
                        <div class="mb-3">
                            <label for="selecttime2" class="form-label">Select Time 2</label>
                            <input type="time" class="form-control" id="selecttime2" name="selecttime2">
                        </div>

                        <div class="mb-3">
                            <label for="selectdate3" class="form-label">Select Date 3</label>
                            <input type="date" class="form-control" id="selectdate3" name="selectdate3">
                        </div>
                        <div class="mb-3">
                            <label for="selecttime3" class="form-label">Select Time 3</label>
                            <input type="time" class="form-control" id="selecttime3" name="selecttime3">
                        </div>

                        <button type="submit" class="btn btn-primary">Schedule</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
