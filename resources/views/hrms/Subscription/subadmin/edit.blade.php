@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Plan</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('subscribe.index') }}">Plans</a></li>
                    <li class="breadcrumb-item active">Edit Plan</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Edit Plan Form -->
    <form action="{{ route('subscribe.update', $plan->id) }}" method="POST" class="shadow p-4 rounded bg-light">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="plan-name" class="form-label">Plan Name</label>
            <input type="text" class="form-control" id="plan-name" name="plan_name" value="{{ $plan->plan_name }}" required>
        </div>

        <div class="mb-3">
            <label for="plan-amount" class="form-label">Plan Amount</label>
            <input type="number" class="form-control" id="plan-amount" name="plan_amount" value="{{ $plan->plan_amount }}" required>
        </div>

        <div class="mb-3">
            <label for="plan-type" class="form-label">Plan Type</label>
            <select class="form-select" id="plan-type" name="plan_type" required>
                <option value="monthly" {{ $plan->plan_type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="annual" {{ $plan->plan_type == 'annual' ? 'selected' : '' }}>Annual</option>
            </select>
        </div>

        <!-- Changed to number input -->
        <div class="mb-3">
            <label for="num-users" class="form-label">No of Users</label>
            <input type="number" class="form-control" id="num-users" name="total_users" value="{{ $plan->total_users }}" required>
        </div>

        <div class="mb-3">
            <label for="plan-description" class="form-label">Plan Description</label>
            <textarea class="form-control" id="plan-description" name="description" rows="3">{{ $plan->description }}</textarea>
        </div>

        <!-- Changed to checkboxes -->
        <div class="mb-3">
            <label class="form-label">Select Modules</label>
            <div class="row">
                @foreach($allModules as $module)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="module-{{ $module->id }}" 
                                name="modules[]" 
                                value="{{ $module->id }}"
                                {{ in_array($module->id, $planModules) ? 'checked' : '' }}>
                            <label class="form-check-label" for="module-{{ $module->id }}">
                                {{ $module->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

     

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Update Plan</button>
        </div>
    </form>
</div>
@endsection
