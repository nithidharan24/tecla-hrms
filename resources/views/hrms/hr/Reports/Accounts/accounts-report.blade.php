@extends('layouts.index')

@section('content')
<style>
  .table-responsive { overflow-x: auto; }
  .btn-export { margin-top: 10px; }
</style>

<div class="content container-fluid">
  <div class="page-header">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-title">Accounts Reports</h3>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
          <li class="breadcrumb-item active">Accounts Reports</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="card tab-box">
    <div class="row user-tabs">
      <div class="col-lg-12 col-md-12 col-sm-12 line-tabs">
        <ul class="nav nav-tabs nav-tabs-bottom">
          <li class="nav-item"><a href="#estimate_report" data-bs-toggle="tab" class="nav-link active">Estimate Report</a></li>
          <li class="nav-item"><a href="#expense_report" data-bs-toggle="tab" class="nav-link">Expense Report</a></li>
          <li class="nav-item"><a href="#invoice_report" data-bs-toggle="tab" class="nav-link">Invoice Report</a></li>
          <li class="nav-item"><a href="#payment_report" data-bs-toggle="tab" class="nav-link">Payment Report</a></li>
          <li class="nav-item"><a href="#budget_expense_report" data-bs-toggle="tab" class="nav-link">Budget Expense Report</a></li>
<li class="nav-item"><a href="#budget_revenue_report" data-bs-toggle="tab" class="nav-link">Budget Revenue Report</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="tab-content">
    <!-- Estimate Report -->
    <div id="estimate_report" class="pro-overview tab-pane fade show active">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
              <button id="exportEstCSV" type="button" class="btn btn-success btn-export">
                <i class="fa fa-file-excel"></i> CSV
              </button>
              <button id="exportEstPDF" type="button" class="btn btn-danger btn-export">
                <i class="fa fa-file-pdf-o"></i> PDF
              </button>
            </div>
          </div>

          <form id="estimateFilterForm">
            <div class="row">
              <div class="col-md-3">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" id="est_from_date">
              </div>
              <div class="col-md-3">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" id="est_to_date">
              </div>
              <div class="col-md-3">
                <label>Status</label>
                <select class="form-control select2" name="status" id="est_status">
                  <option value="">All</option>
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="accepted">Accepted</option>
                  <option value="declined">Declined</option>
                </select>
              </div>
              <div class="col-md-3">
                <label>Client</label>
                <input type="text" class="form-control" name="client" id="est_client" placeholder="Name or text">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-3">
                <label>Estimate ID</label>
                <input type="text" class="form-control" name="estimate_id" id="est_estimate_id" placeholder="EST-0001">
              </div>
              <div class="col-md-9 d-flex align-items-end">
                <div class="ms-auto">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <button type="button" id="resetEstFilters" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-striped custom-table mb-0" id="estimateReportTable" style="width:100%">
            <thead>
              <tr>
                <th>Estimate Date</th>
                <th>Estimate ID</th>
                <th>Client</th>
                <th>Expiry Date</th>
                <th>Grand Amount</th>
                <th>Status</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <!-- Expense Report -->
    <div id="expense_report" class="tab-pane fade">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
              <button id="exportExpCSV" type="button" class="btn btn-success btn-export">
                <i class="fa fa-file-excel"></i> CSV
              </button>
              <button id="exportExpPDF" type="button" class="btn btn-danger btn-export">
                <i class="fa fa-file-pdf-o"></i> PDF
              </button>
            </div>
          </div>

          <form id="expenseFilterForm">
            <div class="row">
              <div class="col-md-3">
                <label>Item Name</label>
                <input type="text" class="form-control" name="item_name" id="exp_item_name">
              </div>
              <div class="col-md-3">
                <label>Paid By</label>
                <select class="form-control select2" name="paid_by" id="exp_paid_by">
                  <option value="">All</option>
                  @foreach(($paidByList ?? []) as $pb)
                    <option value="{{ $pb }}">{{ $pb }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" id="exp_from_date">
              </div>
              <div class="col-md-3">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" id="exp_to_date">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-3">
                <label>Status</label>
                <select class="form-control select2" name="status" id="exp_status">
                  <option value="">All</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
              <div class="col-md-9 d-flex align-items-end">
                <div class="ms-auto">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <button type="button" id="resetExpFilters" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-striped custom-table mb-0" id="expenseReportTable" style="width:100%">
            <thead>
              <tr>
                <th>Created At</th>
                <th>Expense ID</th>
                <th>Item</th>
                <th>Purchase From</th>
                <th>Purchase Date</th>
                <th>Purchased By</th>
                <th>Amount</th>
                <th>Paid By</th>
                <th>Status</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <!-- Invoice Report -->
    <div id="invoice_report" class="tab-pane fade">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
              <button id="exportInvCSV" type="button" class="btn btn-success btn-export">
                <i class="fa fa-file-excel"></i> CSV
              </button>
              <button id="exportInvPDF" type="button" class="btn btn-danger btn-export">
                <i class="fa fa-file-pdf-o"></i> PDF
              </button>
            </div>
          </div>

          <form id="invoiceFilterForm">
            <div class="row">
              <div class="col-md-3">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" id="inv_from_date">
              </div>
              <div class="col-md-3">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" id="inv_to_date">
              </div>
              <div class="col-md-3">
                <label>Status</label>
                <select class="form-control select2" name="status" id="inv_status">
                  <option value="">All</option>
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="pending">Pending</option>
                  <option value="partially_paid">Partially Paid</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                </select>
              </div>
              <div class="col-md-3">
                <label>Client</label>
                <input type="text" class="form-control" name="client" id="inv_client">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-3">
                <label>Invoice ID</label>
                <input type="text" class="form-control" name="invoice_id" id="inv_invoice_id" placeholder="INV-0001">
              </div>
              <div class="col-md-9 d-flex align-items-end">
                <div class="ms-auto">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <button type="button" id="resetInvFilters" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-striped custom-table mb-0" id="invoiceReportTable" style="width:100%">
            <thead>
              <tr>
                <th>Invoice Date</th>
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Due Date</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Discount</th>
                <th>Grand Amount</th>
                <th>Status</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>

    <!-- Payment Report -->
    <div id="payment_report" class="tab-pane fade">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Filters</h4>
            <div>
              <button id="exportPayCSV" type="button" class="btn btn-success btn-export">
                <i class="fa fa-file-excel"></i> CSV
              </button>
              <button id="exportPayPDF" type="button" class="btn btn-danger btn-export">
                <i class="fa fa-file-pdf-o"></i> PDF
              </button>
            </div>
          </div>

          <form id="paymentFilterForm">
            <div class="row">
              <div class="col-md-3">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" id="pay_from_date">
              </div>
              <div class="col-md-3">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" id="pay_to_date">
              </div>
              <div class="col-md-3">
                <label>Invoice ID</label>
                <input type="text" class="form-control" name="invoice_id" id="pay_invoice_id">
              </div>
              <div class="col-md-3">
                <label>Client Name</label>
                <input type="text" class="form-control" name="client_name" id="pay_client_name">
              </div>
            </div>

            <div class="row mt-2">
              <div class="col-md-3">
                <label>Payment Method</label>
                <select class="form-control select2" name="payment_method" id="pay_payment_method">
                  <option value="">All</option>
                  @foreach(($paymentMethods ?? []) as $pm)
                    <option value="{{ $pm }}">{{ $pm }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label>Invoice Status</label>
                <select class="form-control select2" name="status" id="pay_status">
                  <option value="">All</option>
                  <option value="pending">Pending</option>
                  <option value="partially_paid">Partially Paid</option>
                  <option value="paid">Paid</option>
                  <option value="overdue">Overdue</option>
                </select>
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <div class="ms-auto">
                  <button type="submit" class="btn btn-primary">Apply Filters</button>
                  <button type="button" id="resetPayFilters" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-body table-responsive">
          <table class="table table-striped custom-table mb-0" id="paymentReportTable" style="width:100%">
            <thead>
              <tr>
                <th>Payment Date</th>
                <th>Payment ID</th>
                <th>Invoice ID</th>
                <th>Client</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Invoice Total</th>
                <th>Total Paid</th>
                <th>Remaining</th>
                <th>Invoice Status</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
    <!-- Add this to the nav tabs section -->

<!-- Add this tab content section after the other tabs -->
<div id="budget_expense_report" class="tab-pane fade">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Filters</h4>
                <div>
                    <button id="exportBudgetExpCSV" type="button" class="btn btn-success btn-export">
                        <i class="fa fa-file-excel"></i> CSV
                    </button>
                    <button id="exportBudgetExpPDF" type="button" class="btn btn-danger btn-export">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                </div>
            </div>

            <form id="budgetExpenseFilterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category" id="budget_exp_category">
                    </div>
                    <div class="col-md-3">
                        <label>Sub Category</label>
                        <input type="text" class="form-control" name="sub_category" id="budget_exp_sub_category">
                    </div>
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" class="form-control" name="from_date" id="budget_exp_from_date">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" class="form-control" name="to_date" id="budget_exp_to_date">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Notes</label>
                        <input type="text" class="form-control" name="notes" id="budget_exp_notes">
                    </div>
                    <div class="col-md-9 d-flex align-items-end">
                        <div class="ms-auto">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <button type="button" id="resetBudgetExpFilters" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped custom-table mb-0" id="budgetExpenseReportTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Expense Date</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Created At</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add the tab content section -->
<div id="budget_revenue_report" class="tab-pane fade">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-0">Filters</h4>
                <div>
                    <button id="exportBudgetRevCSV" type="button" class="btn btn-success btn-export">
                        <i class="fa fa-file-excel"></i> CSV
                    </button>
                    <button id="exportBudgetRevPDF" type="button" class="btn btn-danger btn-export">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </button>
                </div>
            </div>

            <form id="budgetRevenueFilterForm">
                <div class="row">
                    <div class="col-md-3">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category" id="budget_rev_category">
                    </div>
                    <div class="col-md-3">
                        <label>Sub Category</label>
                        <input type="text" class="form-control" name="sub_category" id="budget_rev_sub_category">
                    </div>
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" class="form-control" name="from_date" id="budget_rev_from_date">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" class="form-control" name="to_date" id="budget_rev_to_date">
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Notes</label>
                        <input type="text" class="form-control" name="notes" id="budget_rev_notes">
                    </div>
                    <div class="col-md-9 d-flex align-items-end">
                        <div class="ms-auto">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <button type="button" id="resetBudgetRevFilters" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped custom-table mb-0" id="budgetRevenueReportTable" style="width:100%">
                <thead>
                    <tr>
                        <th>Revenue Date</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Created At</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
  </div>
</div>

<script>
$(function() {
  // Helper to title-case and prettify statuses
  function toTitle(s) {
    return (s || '').replace(/_/g, ' ').replace(/\b\w/g, function(m){ return m.toUpperCase(); });
  }

  // ESTIMATE
  var estTable = $('#estimateReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('accounts-reports.estimates') }}",
      type: "POST",
      data: function(d) {
        d.from_date  = $('#est_from_date').val();
        d.to_date    = $('#est_to_date').val();
        d.status     = $('#est_status').val();
        d.client     = $('#est_client').val();
        d.estimate_id= $('#est_estimate_id').val();
        d._token     = "{{ csrf_token() }}";
      }
    },
    columns: [
      { data: 'estimate_date', name: 'estimate_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'estimate_id', name: 'estimate_id' },
      { data: 'client_name', name: 'client_name' },
      { data: 'expiry_date', name: 'expiry_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'grant_amt', name: 'grant_amt', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'status', name: 'status', render: function(s){ if(!s) return ''; var cls='bg-secondary'; s=(s||'').toLowerCase(); if(s==='draft')cls='bg-info'; if(s==='sent')cls='bg-primary'; if(s==='accepted')cls='bg-success'; if(s==='declined')cls='bg-danger'; return '<span class="badge '+cls+'">'+toTitle(s)+'</span>'; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No estimates found", zeroRecords: "No matching estimates found" }
  });
  $('#estimateFilterForm').on('submit', function(e){ e.preventDefault(); estTable.ajax.reload(); });
  $('#resetEstFilters').on('click', function(){ $('#estimateFilterForm')[0].reset(); $('.select2').val('').trigger('change'); estTable.ajax.reload(); });

  // EXPENSE
  var expTable = $('#expenseReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('accounts-reports.expenses') }}",
      type: "POST",
      data: function(d) {
        d.item_name = $('#exp_item_name').val();
        d.paid_by   = $('#exp_paid_by').val();
        d.from_date = $('#exp_from_date').val();
        d.to_date   = $('#exp_to_date').val();
        d.status    = $('#exp_status').val();
        d._token    = "{{ csrf_token() }}";
      }
    },
    columns: [
      { data: 'created_at', name: 'created_at', render: function(d){ return d ? new Date(d).toLocaleString() : ''; } },
      { data: 'expense_id', name: 'expense_id' },
      { data: 'item_name', name: 'item_name' },
      { data: 'purchase_from', name: 'purchase_from' },
      { data: 'purchase_date', name: 'purchase_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'purchased_by', name: 'purchased_by' },
      { data: 'amount', name: 'amount', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'paid_by', name: 'paid_by' },
      { data: 'status', name: 'status', render: function(s){ if(!s) return ''; s=(s||'').toLowerCase(); var cls='bg-secondary'; if(s==='approved')cls='bg-success'; if(s==='pending')cls='bg-warning'; if(s==='rejected')cls='bg-danger'; return '<span class="badge '+cls+'">'+toTitle(s)+'</span>'; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No expenses found", zeroRecords: "No matching expenses found" }
  });
  $('#expenseFilterForm').on('submit', function(e){ e.preventDefault(); expTable.ajax.reload(); });
  $('#resetExpFilters').on('click', function(){ $('#expenseFilterForm')[0].reset(); $('.select2').val('').trigger('change'); expTable.ajax.reload(); });
  $('#exportExpCSV').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-expenses') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#expenseFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });
  $('#exportExpPDF').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-expenses-pdf') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#expenseFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });
// Add these inside the $(function() {...}); section, similar to the other export handlers

$('#exportEstCSV').on('click', function(e){
  e.preventDefault();
  var form = $('<form>', { action: "{{ route('accounts-reports.export-estimates') }}", method: 'POST', target: '_blank' })
    .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
  $('#estimateFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
  $('body').append(form); form.submit(); form.remove();
});

$('#exportEstPDF').on('click', function(e){
  e.preventDefault();
  var form = $('<form>', { action: "{{ route('accounts-reports.export-estimates-pdf') }}", method: 'POST', target: '_blank' })
    .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
  $('#estimateFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
  $('body').append(form); form.submit(); form.remove();
});
  // INVOICE
  var invTable = $('#invoiceReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('accounts-reports.invoices') }}",
      type: "POST",
      data: function(d) {
        d.from_date  = $('#inv_from_date').val();
        d.to_date    = $('#inv_to_date').val();
        d.status     = $('#inv_status').val();
        d.client     = $('#inv_client').val();
        d.invoice_id = $('#inv_invoice_id').val();
        d._token     = "{{ csrf_token() }}";
      }
    },
    columns: [
      { data: 'invoice_date', name: 'invoice_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'invoice_id', name: 'invoice_id' },
      { data: 'client_name', name: 'client_name' },
      { data: 'due_date', name: 'due_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'total', name: 'total', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'tax_amt', name: 'tax_amt', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'discount', name: 'discount', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'grant_amt', name: 'grant_amt', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'status', name: 'status', render: function(s){ if(!s) return ''; s=(s||'').toLowerCase(); var cls='bg-secondary'; if(s==='draft')cls='bg-info'; if(s==='sent')cls='bg-primary'; if(s==='pending')cls='bg-warning'; if(s==='overdue')cls='bg-danger'; if(s==='partially_paid')cls='bg-dark'; if(s==='paid')cls='bg-success'; return '<span class="badge '+cls+'">'+toTitle(s)+'</span>'; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No invoices found", zeroRecords: "No matching invoices found" }
  });
  $('#invoiceFilterForm').on('submit', function(e){ e.preventDefault(); invTable.ajax.reload(); });
  $('#resetInvFilters').on('click', function(){ $('#invoiceFilterForm')[0].reset(); $('.select2').val('').trigger('change'); invTable.ajax.reload(); });
  $('#exportInvCSV').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-invoices') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#invoiceFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });
  $('#exportInvPDF').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-invoices-pdf') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#invoiceFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });

  // PAYMENT
  var payTable = $('#paymentReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "{{ route('accounts-reports.payments') }}",
      type: "POST",
      data: function(d) {
        d.from_date      = $('#pay_from_date').val();
        d.to_date        = $('#pay_to_date').val();
        d.invoice_id     = $('#pay_invoice_id').val();
        d.client_name    = $('#pay_client_name').val();
        d.payment_method = $('#pay_payment_method').val();
        d.status         = $('#pay_status').val();
        d._token         = "{{ csrf_token() }}";
      }
    },
    columns: [
      { data: 'payment_date', name: 'payment_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
      { data: 'payment_id', name: 'payment_id' },
      { data: 'invoice_id', name: 'invoice_id' },
      { data: 'client_name', name: 'client_name' },
      { data: 'amount', name: 'amount', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'payment_method', name: 'payment_method' },
      { data: 'invoice_total', name: 'invoice_total', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'total_paid', name: 'total_paid', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'remaining_amount', name: 'remaining_amount', render: function(d){ return d == null ? '' : Number(d).toFixed(2); } },
      { data: 'status', name: 'status', render: function(s){ if(!s) return ''; s=(s||'').toLowerCase(); var cls='bg-secondary'; if(s==='pending')cls='bg-warning'; if(s==='overdue')cls='bg-danger'; if(s==='partially_paid')cls='bg-dark'; if(s==='paid')cls='bg-success'; return '<span class="badge '+cls+'">'+toTitle(s)+'</span>'; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No payments found", zeroRecords: "No matching payments found" }
  });
  $('#paymentFilterForm').on('submit', function(e){ e.preventDefault(); payTable.ajax.reload(); });
  $('#resetPayFilters').on('click', function(){ $('#paymentFilterForm')[0].reset(); $('.select2').val('').trigger('change'); payTable.ajax.reload(); });
  $('#exportPayCSV').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-payments') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#paymentFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });
  $('#exportPayPDF').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { action: "{{ route('accounts-reports.export-payments-pdf') }}", method: 'POST', target: '_blank' })
      .append($('<input>', { type:'hidden', name:'_token', value:"{{ csrf_token() }}" }));
    $('#paymentFilterForm').find('input,select').each(function(){ if(this.name) form.append($('<input>',{type:'hidden', name:this.name, value:$(this).val()})); });
    $('body').append(form); form.submit(); form.remove();
  });
  // BUDGET EXPENSE
var budgetExpTable = $('#budgetExpenseReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('accounts-reports.budget-expenses') }}",
        type: "POST",
        data: function(d) {
            d.category = $('#budget_exp_category').val();
            d.sub_category = $('#budget_exp_sub_category').val();
            d.from_date = $('#budget_exp_from_date').val();
            d.to_date = $('#budget_exp_to_date').val();
            d.notes = $('#budget_exp_notes').val();
            d._token = "{{ csrf_token() }}";
        }
    },
    columns: [
        { data: 'expense_date', name: 'expense_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
        { data: 'category_name', name: 'category_name', defaultContent: 'N/A' },
        { data: 'subcategory_name', name: 'subcategory_name', defaultContent: 'N/A' },
        { 
            data: 'amount', 
            name: 'amount', 
            render: function(d, type, row){ 
                return (row.currency_symbol || '') + ' ' + (d == null ? '0.00' : Number(d).toFixed(2)); 
            } 
        },
        { data: 'Notes', name: 'Notes' },
        { data: 'created_at', name: 'created_at', render: function(d){ return d ? new Date(d).toLocaleString() : ''; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No budget expenses found", zeroRecords: "No matching budget expenses found" }
});

$('#budgetExpenseFilterForm').on('submit', function(e){ 
    e.preventDefault(); 
    budgetExpTable.ajax.reload(); 
});

$('#resetBudgetExpFilters').on('click', function(){ 
    $('#budgetExpenseFilterForm')[0].reset(); 
    budgetExpTable.ajax.reload(); 
});

$('#exportBudgetExpCSV').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { 
        action: "{{ route('accounts-reports.export-budget-expenses') }}", 
        method: 'POST', 
        target: '_blank' 
    }).append($('<input>', { 
        type:'hidden', 
        name:'_token', 
        value:"{{ csrf_token() }}" 
    }));
    $('#budgetExpenseFilterForm').find('input,select').each(function(){ 
        if(this.name) form.append($('<input>',{
            type:'hidden', 
            name:this.name, 
            value:$(this).val()
        })); 
    });
    $('body').append(form); 
    form.submit(); 
    form.remove();
});

$('#exportBudgetExpPDF').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { 
        action: "{{ route('accounts-reports.export-budget-expenses-pdf') }}", 
        method: 'POST', 
        target: '_blank' 
    }).append($('<input>', { 
        type:'hidden', 
        name:'_token', 
        value:"{{ csrf_token() }}" 
    }));
    $('#budgetExpenseFilterForm').find('input,select').each(function(){ 
        if(this.name) form.append($('<input>',{
            type:'hidden', 
            name:this.name, 
            value:$(this).val()
        })); 
    });
    $('body').append(form); 
    form.submit(); 
    form.remove();
});
// BUDGET REVENUE
var budgetRevTable = $('#budgetRevenueReportTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('accounts-reports.budget-revenue') }}",
        type: "POST",
        data: function(d) {
            d.category = $('#budget_rev_category').val();
            d.sub_category = $('#budget_rev_sub_category').val();
            d.from_date = $('#budget_rev_from_date').val();
            d.to_date = $('#budget_rev_to_date').val();
            d.notes = $('#budget_rev_notes').val();
            d._token = "{{ csrf_token() }}";
        }
    },
    columns: [
        { data: 'revenue_date', name: 'revenue_date', render: function(d){ return d ? new Date(d).toLocaleDateString('en-GB') : ''; } },
        { data: 'category_name', name: 'category_name', defaultContent: 'N/A' },
        { data: 'subcategory_name', name: 'subcategory_name', defaultContent: 'N/A' },
        { 
            data: 'amount', 
            name: 'amount', 
            render: function(d, type, row){ 
                return (row.currency_symbol || '') + ' ' + (d == null ? '0.00' : Number(d).toFixed(2)); 
            } 
        },
        { data: 'Notes', name: 'Notes' },
        { data: 'created_at', name: 'created_at', render: function(d){ return d ? new Date(d).toLocaleString() : ''; } },
    ],
    order: [[0, 'desc']],
    language: { emptyTable: "No budget revenue found", zeroRecords: "No matching budget revenue found" }
});

$('#budgetRevenueFilterForm').on('submit', function(e){ 
    e.preventDefault(); 
    budgetRevTable.ajax.reload(); 
});

$('#resetBudgetRevFilters').on('click', function(){ 
    $('#budgetRevenueFilterForm')[0].reset(); 
    budgetRevTable.ajax.reload(); 
});

$('#exportBudgetRevCSV').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { 
        action: "{{ route('accounts-reports.export-budget-revenue') }}", 
        method: 'POST', 
        target: '_blank' 
    }).append($('<input>', { 
        type:'hidden', 
        name:'_token', 
        value:"{{ csrf_token() }}" 
    }));
    $('#budgetRevenueFilterForm').find('input,select').each(function(){ 
        if(this.name) form.append($('<input>',{
            type:'hidden', 
            name:this.name, 
            value:$(this).val()
        })); 
    });
    $('body').append(form); 
    form.submit(); 
    form.remove();
});

$('#exportBudgetRevPDF').on('click', function(e){
    e.preventDefault();
    var form = $('<form>', { 
        action: "{{ route('accounts-reports.export-budget-revenue-pdf') }}", 
        method: 'POST', 
        target: '_blank' 
    }).append($('<input>', { 
        type:'hidden', 
        name:'_token', 
        value:"{{ csrf_token() }}" 
    }));
    $('#budgetRevenueFilterForm').find('input,select').each(function(){ 
        if(this.name) form.append($('<input>',{
            type:'hidden', 
            name:this.name, 
            value:$(this).val()
        })); 
    });
    $('body').append(form); 
    form.submit(); 
    form.remove();
});
});

</script>
@endsection