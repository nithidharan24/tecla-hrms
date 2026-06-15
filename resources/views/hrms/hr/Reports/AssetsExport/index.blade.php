@extends('layouts.index')

@section('title', 'Asset Reports')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Asset Reports</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/admin-dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Asset Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Search Filter -->
        <form method="GET" action="{{ route('asset-reports.index') }}">
            <div class="row filter-row">
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <input type="text" name="employee_name" class="form-control floating" 
                               placeholder="Employee Name" value="{{ request('employee_name') }}">
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <select name="status" class="form-control floating select">
                            <option value="">Select Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        <label class="focus-label">Status</label>
                    </div>
                </div>
                @if(session('role') === 'admin')
                <div class="col-sm-6 col-md-3">
                    <div class="input-block mb-3 form-focus">
                        <select name="branch" class="form-control floating select">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        <label class="focus-label">Branch</label>
                    </div>
                </div>
                @endif
                <div class="col-sm-6 col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('asset-reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
        <!-- /Search Filter -->

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        @if(request()->hasAny(['employee_name', 'status', 'branch']))
                            <span class="badge badge-info">Filtered Results</span>
                        @else
                            <span class="badge badge-light">All Records</span>
                        @endif
                    </div>
                    <div>
                     <form method="GET" action="{{ route('asset-reports.index') }}" class="form-inline">
    @foreach(request()->all() as $key => $value)
        @if($key != 'export')
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit" name="export" value="csv" class="btn btn-success btn-sm mr-2">
        <i class="fas fa-file-csv"></i> Export CSV
    </button>
    <button type="submit" name="export" value="pdf" class="btn btn-danger btn-sm">
        <i class="fas fa-file-pdf"></i> Export PDF
    </button>
</form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped custom-table mb-0 datatable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Asset ID</th>
                                <th>Asset Name</th>
                                <th>Assigned To</th>
                                <th>Branch</th>
                                <th>Purchase Date</th>
                                <th>Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $asset->asset_id }}</td>
                                    <td>{{ $asset->asset_name }}</td>
                                    <td>
                                        @if($asset->firstname)
                                            {{ $asset->firstname }} {{ $asset->lastname }}
                                            @if($asset->designation_name)
                                                <br><small class="text-muted">{{ $asset->designation_name }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $asset->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($asset->purchase_date)->format('d M Y') }}</td>
                                    <td>{{ number_format($asset->value, 2) }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($asset->status == 'approved') badge-success
                                            @elseif($asset->status == 'pending') badge-warning
                                            @elseif($asset->status == 'returned') badge-secondary
                                            @endif">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No Asset Data Found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

              
            </div>
        </div>
    </div>
</div>

<!-- Asset Details Modal -->
<div class="modal fade" id="assetDetailsModal" tabindex="-1" role="dialog" aria-labelledby="assetDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="assetDetailsModalLabel">Asset Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="assetDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // View asset details
    $('.view-asset-details').click(function() {
        var assetId = $(this).data('asset-id');
        var url = "{{ route('assets.show', ':id') }}".replace(':id', assetId);
        
        $.get(url, function(data) {
            $('#assetDetailsContent').html(data);
            $('#assetDetailsModal').modal('show');
        }).fail(function() {
            alert('Failed to load asset details');
        });
    });
});
</script>

@endsection