@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <h3>Edit Subscribed Company</h3>
    <form action="{{ route('subscribecompany.update', $company->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Company details -->
        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" name="company_name" value="{{ $company->company_name }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Company Number</label>
            <input type="text" name="company_number" value="{{ $company->company_number }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Address</label>
            <input type="text" name="company_address" value="{{ $company->company_address }}" class="form-control" required>
        </div>
<!-- Users -->
<div class="mb-3">
    <label>Users</label>
    <input type="number" name="users" value="{{ $company->users }}" class="form-control" required>
</div>

        <!-- Plans -->
        <div class="mb-3">
            <label>Plan</label>
            <select name="plan_id" class="form-select" required>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ $company->plan_id == $plan->id ? 'selected' : '' }}>
                        {{ $plan->plan_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Modules -->
        <div class="mb-3">
            <label><strong>Modules</strong></label><br>
            @foreach($allModules as $module)
                <div class="form-check">
                    <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                        class="form-check-input"
                        {{ in_array($module->id, $subscribedModules) ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $module->name }}</label>
                </div>
            @endforeach
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-select" required>
                <option value="active" {{ $company->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $company->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ $company->status == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection
