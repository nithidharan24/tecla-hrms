@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add Plan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Add Plan</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Plan Form -->
    <form action="{{ route('subscribe.store') }}" method="POST" class="shadow p-4 rounded bg-light">
        @csrf 

        <div class="mb-3">
            <label for="plan-name" class="form-label">Plan Name</label>
            <input type="text" class="form-control" id="plan-name" name="plan_name" required>
        </div>

        <div class="mb-3">
            <label for="plan-amount" class="form-label">Plan Amount</label>
            <input type="number" class="form-control" id="plan-amount" name="plan_amount" required>
        </div>

        <div class="mb-3">
            <label for="plan-type" class="form-label">Plan Type</label>
            <select class="form-select" id="plan-type" name="plan_type" required>
                <option value="" disabled selected>Select Plan Type</option>
                <option value="monthly">Monthly</option>
                <option value="annual">Annual</option>
            </select>
        </div>

        <!-- Changed from dropdown to number input -->
        <div class="mb-3">
            <label for="num-users" class="form-label">No of Users</label>
            <input type="number" class="form-control" id="num-users" name="num_users" min="1" placeholder="Enter number of users" required>
        </div>

        <!-- Changed from multi-select to checkboxes -->
        <div class="mb-3">
            <label class="form-label">Select Modules</label>
            <div class="row">
                @foreach(DB::table('modules')->get() as $module)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="module-{{ $module->id }}" name="modules[]" value="{{ $module->id }}">
                            <label class="form-check-label" for="module-{{ $module->id }}">
                                {{ $module->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label for="plan-description" class="form-label">Plan Description</label>
            <textarea class="form-control" id="plan-description" name="plan_description" rows="3" required></textarea>
        </div>

       

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Submit Plan</button>
        </div>
    </form>
</div>
@endsection
