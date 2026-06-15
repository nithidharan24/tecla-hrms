@extends('layouts.index')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
            
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Invoice Report</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item active">Invoice Report</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->
        
        @if(session('success'))
            <div id="successMessage" class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Filter Invoices</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('invoice-reports.index') }}" id="filterForm">
                    <div class="row filter-row">
                        <!-- Client Dropdown -->
                        <div class="col-sm-6 col-md-3"> 
                            <div class="input-block mb-3 form-focus select-focus">
                                <select name="client" class="form-select floating">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>
                                            {{ $client->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- From Date -->
                        <div class="col-sm-6 col-md-3">  
                            <div class="input-block mb-3 form-focus">
                                <div class="cal-icon">
                                    <input name="from_date" class="form-control floating datetimepicker" type="text" 
                                           placeholder="dd-mm-yyyy" value="{{ request('from_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <!-- To Date -->
                        <div class="col-sm-6 col-md-3">  
                            <div class="input-block mb-3 form-focus">
                                <div class="cal-icon">
                                    <input name="to_date" class="form-control floating datetimepicker" type="text" 
                                           placeholder="dd-mm-yyyy" value="{{ request('to_date') }}" autocomplete="off">
                                </div>
                            </div>
                        </div>

                      
                    </div>

                    <div class="row">
                        <!-- Search and Reset Buttons -->
                        <div class="col-sm-6 col-md-6">  
                            <button type="submit" class="btn btn-success me-2">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <a href="{{ route('invoice-reports.index') }}" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> Reset
                            </a>
                        </div>     
                    </div>
                </form>
            </div>
        </div>

        <!-- Export Buttons -->
        @if(count($invoices) > 0)
        <div class="row mb-3">
            <div class="col-md-12 text-end">
                <div class="btn-group">
                    <a href="{{ route('invoice-reports.export.csv', request()->query()) }}" class="btn btn-primary">
                        <i class="fa fa-file-excel"></i> Export to CSV
                    </a>
                    <a href="{{ route('invoice-reports.export.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="fa fa-file-pdf"></i> Export to PDF
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Results Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if(count($invoices) > 0)
                    <div class="table-responsive">
    <table class="table custom-table mb-0" id="invoiceTable">
        <thead>
            <tr>
                
                <th>S.No</th>
                <th>Invoice Number</th>
                <th>Client</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr class="row-check" id="invoice-row-{{ $invoice->id }}">
                <td data-label="S.No">{{ $loop->iteration }}</td>

                <td data-label="Invoice ID">
                    <span class="high">{{ $invoice->invoice_id ?? 'N/A' }}</span>
                </td>
                
                <td data-label="Client Name">
                    <span class="od-chip-highlight">{{ $invoice->client_name ?? 'N/A' }}</span>
                </td>
                
                <td data-label="Invoice Date">
                    {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') : 'N/A' }}
                </td>
                
                <td data-label="Due Date">
                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') : 'N/A' }}
                </td>
                
                <td data-label="Grand Amount">
                    {{ number_format((float)$invoice->grant_amt, 2) }}
                </td>
                
                <td data-label="Status">
                    @php
                        $statusClasses = [
                            'sent' => 'info',
                            'paid' => 'success',
                            'partially paid' => 'warning'
                        ];
                        $status = strtolower($invoice->status ?? '');
                        $badgeClass = $statusClasses[$status] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($invoice->status ?? 'Unknown') }}</span>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>



                        @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-file-text-o fa-3x text-muted"></i>
                            </div>
                            <h5>No invoices found</h5>
                           
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this invoice? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success message after 5 seconds
    setTimeout(function() {
        var successMessage = document.getElementById('successMessage');
        if (successMessage) {
            successMessage.style.transition = 'opacity 1s ease';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.remove();
            }, 1000);
        }
    }, 5000);

    // Initialize date pickers
    if (typeof $.fn.datetimepicker !== 'undefined') {
        $('.datetimepicker').datetimepicker({
            format: 'DD-MM-YYYY',
            icons: {
                up: 'fa fa-angle-up',
                down: 'fa fa-angle-down',
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            }
        });
    }

    // Initialize DataTable if available
    if (typeof $.fn.DataTable !== 'undefined' && document.getElementById('invoiceTable')) {
        $('#invoiceTable').DataTable({
            "pageLength": 25,
            "ordering": true,
            "searching": false,
            "lengthChange": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    }
});

function viewInvoice(id) {
    // Implement view functionality
    window.location.href = `/invoices/${id}`;
}

function editInvoice(id) {
    // Implement edit functionality
    window.location.href = `/invoices/${id}/edit`;
}

function deleteInvoice(id) {
    document.getElementById('deleteForm').action = `{{ route('invoice-reports.destroy', '') }}/${id}`;
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('checkAllInvoices');
    const rowCheckboxes = document.querySelectorAll('#invoiceTable .row-checkbox');

    selectAllCheckbox.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => {
            cb.checked = selectAllCheckbox.checked;
            toggleRowHighlight(cb.closest('.row-check'), cb.checked);
        });
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            toggleRowHighlight(cb.closest('.row-check'), cb.checked);
            selectAllCheckbox.checked = Array.from(rowCheckboxes).every(c => c.checked);
        });
    });

    function toggleRowHighlight(row, isChecked) {
        if(isChecked) row.classList.add('od-selected');
        else row.classList.remove('od-selected');
    }
});
</script>

<style>
.filter-row .input-block {
    margin-bottom: 1rem;
}

.badge {
    font-size: 0.75em;
    padding: 0.375rem 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.btn-group .btn {
    margin-right: 0.5rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.dropdown-toggle::after {
    margin-left: 0.5rem;
}
</style>
@endsection