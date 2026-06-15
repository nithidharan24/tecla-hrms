@extends('layouts.index')

@section('content')

<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
 
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Client Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Client Reports</li>
                    </ul>
                </div>
            </div>
        </div>
  

      
    <form action="{{ route('client-reports.index') }}" method="GET">
        <div class="row filter-row">
            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <input type="text" name="client_id" class="form-control floating" value="{{ request('client_id') }}">
                    <label class="focus-label">Client ID</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <input type="text" name="client_name" class="form-control floating" value="{{ request('client_name') }}">
                    <label class="focus-label">Client Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <select name="company_name" class="form-control floating select">
                        <option value="">-- Select Company --</option>
                        @foreach($companyNames as $companyName)
                            <option value="{{ $companyName }}" {{ request('company_name') == $companyName ? 'selected' : '' }}>
                                {{ $companyName }}
                            </option>
                        @endforeach
                    </select>
                    <label class="focus-label">Company Name</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="input-block mb-3 form-focus">
                    <select name="status" class="form-control floating select">
                        <option value="">-- Select Status --</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <label class="focus-label">Status</label>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <button type="submit" class="btn btn-success w-100">Search</button>
            </div>
        </div>
    </form>
      
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <a href="{{ route('client-reports.export.csv', request()->query()) }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export to CSV
                </a>
                <a href="{{ route('client-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                    <i class="fa fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>
      

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table custom-table datatable" id="projects-table">
        <thead>
            <tr>
                
                <th>Client ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Services</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $key => $client)
                <tr>
                    <td data-label="Client ID"><span class="high">{{ $client->client_id }}</span></td>

                    <td data-label="First Name"><span class="od-chip-highlight">{{ ucfirst($client->first_name) }}</span></td>
                    
                    <td data-label="Last Name">{{ ucfirst($client->last_name) }}</td>
                    
                    <td data-label="Company Name">{{ $client->company_name }}</td>
                    
                    <td data-label="Email">{{ $client->email }}</td>
                    
                    <td data-label="Phone">{{ $client->phone }}</td>
                    
                    <td data-label="Status">
                        <div class="dropdown action-label">
                            <a href="#" class="btn btn-white btn-sm btn-rounded">
                                <i class="fa-regular fa-circle-dot text-{{ $client->status == 'active' ? 'success' : ($client->status == 'pending' ? 'warning' : 'danger') }}"></i>
                                {{ ucfirst($client->status) }}
                            </a>
                        </div>
                    </td>
                    
                    <td data-label="Services">
                        @php
                            $clientServiceIds = json_decode($client->services ?? '[]', true);
                            $displayedServices = [];
                            if (is_array($clientServiceIds)) {
                                foreach ($clientServiceIds as $serviceId) {
                                    if (isset($servicesMap[$serviceId])) {
                                        $displayedServices[] = $servicesMap[$serviceId];
                                    }
                                }
                            }
                        @endphp
                        @foreach($displayedServices as $serviceName)
                            <span class="badge bg-info text-white">{{ $serviceName }}</span>
                        @endforeach
                    </td>
                    
                
                    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all / row highlight
    $('#checkAllClients').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>

            </div>
        </div>
    </div>

</div>

@endsection
