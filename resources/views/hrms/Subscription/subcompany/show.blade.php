@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Subscribed Companies</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="table-responsive mt-4">
        <table class="table table-striped datatable">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Number</th>
                    <th>Address</th>
                    <th>Plan</th>
                    <th>Plan Type</th>
                    <th>Amount</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Actions</th> <!-- 👈 New Column -->
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td data-label="Company Name">{{ $company->company_name }}</td>

                    <td data-label="Company Number">{{ $company->company_number }}</td>
                    
                    <td data-label="Address">{{ $company->company_address }}</td>
                    
                    <td data-label="Plan Name">{{ $company->plan_name }}</td>
                    
                    <td data-label="Plan Type">{{ ucfirst($company->plan_type) }}</td>
                    
                    <td data-label="Plan Amount">${{ $company->plan_amount }}</td>
                    
                    <td data-label="Start Date">{{ \Carbon\Carbon::parse($company->start_date)->format('d M Y') }}</td>
                    
                    <td data-label="End Date">{{ \Carbon\Carbon::parse($company->end_date)->format('d M Y') }}</td>
                    
                    <td data-label="Status">
                        <span class="badge bg-{{ $company->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </td>
                    
                    <td data-label="Edit">
                        <a href="{{ route('subscribecompany.edit', $company->id) }}" class="btn btn-sm btn-primary">
                            Edit
                        </a>
                    </td>
                    
                    <td data-label="Change Status">
                        <form action="{{ route('subscribecompany.updateStatus', $company->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="active" {{ $company->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $company->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ $company->status === 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </form>
                    </td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
