@extends('layouts.index')

@section('content')
<div class="content container-fluid">

    {{-- ───────────── Page header ───────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0"><i class="fas fa-users text-primary me-2"></i>{{ $department->department }}</h4>
            <small class="text-muted">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }} — Release per employee, selected, or all at once</small>
        </div>
        <a href="{{ route('salary-release.index', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    {{-- ───────────── Summary strip ───────────── --}}
    @php
        $sentTotal    = $payslips->where('email_sent', 1)->count();
        $pendingTotal = $payslips->count() - $sentTotal;
        $netTotal     = $payslips->sum('net_salary');
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="sr-stat">
                <span class="sr-stat-label">Employees</span>
                <span class="sr-stat-value">{{ $payslips->count() }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sr-stat">
                <span class="sr-stat-label">Total Net Payout</span>
                <span class="sr-stat-value">₹{{ number_format($netTotal, 0) }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sr-stat">
                <span class="sr-stat-label">Sent</span>
                <span class="sr-stat-value text-success">{{ $sentTotal }}</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="sr-stat">
                <span class="sr-stat-label">Pending</span>
                <span class="sr-stat-value text-warning">{{ $pendingTotal }}</span>
            </div>
        </div>
    </div>

    {{-- Release ALL in department (separate form, outside bulk form) --}}
    <div class="text-end mb-3">
        <form method="POST" action="{{ route('salary-release.release-department') }}" class="d-inline"
              onsubmit="return confirm('Release payslips to ALL {{ count($payslips) }} employee(s) in {{ $department->department }}?');">
            @csrf
            <input type="hidden" name="department_id" value="{{ $departmentId }}">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane"></i> Release All in Department
            </button>
        </form>
    </div>

    {{-- ───────────── Bulk release form wraps the table ───────────── --}}
    <form id="bulkReleaseForm" method="POST" action="{{ route('salary-release.release-selected') }}"
          onsubmit="return confirmBulk();">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">

        <div class="card sr-card">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:42px;" class="text-center">
                                <input type="checkbox" id="selectAll" class="form-check-input sr-check">
                            </th>
                            <th>Employee</th>
                            <th>Employee ID</th>
                            <th>Designation</th>
                            <th class="text-end">Net Salary</th>
                            <th>Email Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payslips as $p)
                        @php
                            // Build earnings rows (non-zero only)
                            $earnings = array_filter([
                                ['Basic Salary',    (float)($p->basic_salary ?? 0)],
                                ['HRA',             (float)($p->hra ?? 0)],
                                ['DA',              (float)($p->da ?? 0)],
                                ['Conveyance',      (float)($p->conveyance ?? 0)],
                                ['Other Allowance', (float)($p->allowance ?? 0)],
                                ['Medical',         (float)($p->medical ?? 0)],
                                ['Staff Welfare',   (float)($p->welfare ?? 0)],
                            ], fn($r) => $r[1] != 0);

                            // Itemized dynamic additions (expense reimbursement etc.)
                            $addItems = $p->additions_items ?? [];

                            $deductions = array_filter([
                                ['PF',               (float)($p->pf ?? 0)],
                                ['ESI',              (float)($p->esi ?? 0)],
                                ['TDS',              (float)($p->tds ?? 0)],
                                ['Professional Tax', (float)($p->tax ?? 0)],
                                ['LOP Deduction',    (float)($p->lop_deduction ?? 0)],
                                ['Employee Leave',   (float)($p->employee_leave_deduction ?? 0)],
                            ], fn($r) => $r[1] != 0);

                            $dedItems = $p->deductions_items ?? [];
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="payslip_ids[]" value="{{ $p->id }}"
                                       class="form-check-input sr-check row-check"
                                       data-net="{{ $p->net_salary }}">
                            </td>
                            <td>
                                <strong>{{ $p->firstname }} {{ $p->lastname }}</strong><br>
                                <small class="text-muted">{{ $p->email ?: 'No email' }}</small>
                            </td>
                            <td>{{ $p->employeeid }}</td>
                            <td>{{ $p->designation_name ?? 'N/A' }}</td>
                            <td class="text-end"><strong class="text-success">₹{{ number_format($p->net_salary, 2) }}</strong></td>
                            <td>
                                @if($p->email_sent)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Sent</span><br>
                                    <small class="text-muted">{{ $p->email_sent_at ? \Carbon\Carbon::parse($p->email_sent_at)->format('d M, h:i A') : '' }}</small>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="toggleDetail('detail-{{ $p->id }}', this)">
                                    <i class="fas fa-list-ul"></i> Breakdown
                                </button>
                                <button type="button"
                                        class="btn btn-sm {{ $p->email_sent ? 'btn-outline-success' : 'btn-success' }}"
                                        {{ $p->email ? '' : 'disabled' }}
                                        onclick="releaseOne({{ $p->id }}, '{{ addslashes($p->firstname.' '.$p->lastname) }}', '{{ $p->email }}')">
                                    <i class="fas fa-envelope"></i> {{ $p->email_sent ? 'Resend' : 'Send' }}
                                </button>
                            </td>
                        </tr>

                        {{-- ───── Expandable breakdown row ───── --}}
                        <tr id="detail-{{ $p->id }}" class="sr-detail-row" style="display:none;">
                            <td colspan="7" class="p-0">
                                <div class="sr-detail">
                                    <div class="row g-4">
                                        {{-- Earnings --}}
                                        <div class="col-md-6">
                                            <div class="sr-detail-head text-success"><i class="fas fa-arrow-up me-1"></i> Earnings</div>
                                            <table class="table table-sm sr-detail-table mb-0">
                                                @foreach($earnings as $e)
                                                    <tr><td>{{ $e[0] }}</td><td class="text-end">₹{{ number_format($e[1], 2) }}</td></tr>
                                                @endforeach
                                                @if(count($addItems))
                                                    <tr><td colspan="2" class="sr-detail-sub">Additions</td></tr>
                                                    @foreach($addItems as $a)
                                                        <tr>
                                                            <td class="ps-3">{{ $a['name'] ?? 'Addition' }}
                                                                @if(!empty($a['category']))<span class="badge bg-light text-muted ms-1">{{ $a['category'] }}</span>@endif
                                                            </td>
                                                            <td class="text-end">₹{{ number_format($a['amount'] ?? 0, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                                <tr class="sr-detail-total">
                                                    <td>Total Earnings</td>
                                                    <td class="text-end">₹{{ number_format($p->total_earnings ?? 0, 2) }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        {{-- Deductions --}}
                                        <div class="col-md-6">
                                            <div class="sr-detail-head text-danger"><i class="fas fa-arrow-down me-1"></i> Deductions</div>
                                            <table class="table table-sm sr-detail-table mb-0">
                                                @forelse($deductions as $d)
                                                    <tr><td>{{ $d[0] }}</td><td class="text-end">₹{{ number_format($d[1], 2) }}</td></tr>
                                                @empty
                                                    <tr><td colspan="2" class="text-muted text-center">No deductions</td></tr>
                                                @endforelse
                                                @foreach($dedItems as $d)
                                                    <tr><td>{{ $d['name'] ?? 'Deduction' }}</td><td class="text-end">₹{{ number_format($d['amount'] ?? 0, 2) }}</td></tr>
                                                @endforeach
                                                <tr class="sr-detail-total">
                                                    <td>Total Deductions</td>
                                                    <td class="text-end">₹{{ number_format($p->total_deductions ?? 0, 2) }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="sr-detail-net">
                                        Net Pay <strong>₹{{ number_format($p->net_salary, 2) }}</strong>
                                        @if(($p->lop_days ?? 0) > 0)
                                            <span class="ms-3 text-muted">LOP days: {{ $p->lop_days }}</span>
                                        @endif
                                        <span class="ms-3 text-muted">Working days: {{ $p->actual_working_days ?? 0 }}/{{ $p->total_working_days ?? 0 }}</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No payslips found for this department.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    {{-- Hidden single-release form (avoids nested <form> inside bulk form) --}}
    <form id="singleReleaseForm" method="POST" action="{{ route('salary-release.release-employee') }}" class="d-none">
        @csrf
        <input type="hidden" name="payslip_id" value="">
    </form>

    {{-- ───────────── Floating bulk action bar ───────────── --}}
    <div id="bulkBar" class="sr-bulk-bar">
        <div>
            <span id="bulkCount">0</span> selected
            <span class="text-muted ms-2">·</span>
            <span class="ms-2">Net: ₹<span id="bulkNet">0</span></span>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-outline-light me-2" onclick="clearSelection()">Clear</button>
            <button type="button" class="btn btn-sm btn-light text-success fw-semibold" onclick="submitBulk()">
                <i class="fas fa-paper-plane"></i> Release Selected
            </button>
        </div>
    </div>
</div>

{{-- ───────────── Scoped styles (premium minimal) ───────────── --}}
<style>
.sr-stat{background:#fff;border:1px solid #eef0f4;border-radius:14px;padding:16px 18px;display:flex;flex-direction:column;gap:4px;height:100%;}
.sr-stat-label{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8a93a6;font-weight:600;}
.sr-stat-value{font-size:22px;font-weight:700;color:#1f2533;}
.sr-card{border:1px solid #eef0f4;border-radius:16px;overflow:hidden;}
.sr-check{cursor:pointer;width:18px;height:18px;}
.sr-detail-row > td{background:#fafbfc;}
.sr-detail{padding:20px 24px;}
.sr-detail-head{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;}
.sr-detail-table td{padding:6px 10px;border-color:#f0f1f5;font-size:13px;}
.sr-detail-table tr:last-child td{border-bottom:none;}
.sr-detail-total td{font-weight:700;border-top:1px solid #e3e6ec !important;color:#1f2533;}
.sr-detail-net{margin-top:14px;padding-top:12px;border-top:1px dashed #d9dde6;font-size:14px;}
.sr-detail-net strong{font-size:16px;color:#198754;}
.sr-bulk-bar{position:fixed;left:50%;transform:translateX(-50%) translateY(120%);bottom:24px;
    display:flex;align-items:center;gap:24px;background:#1f2533;color:#fff;
    padding:12px 20px;border-radius:14px;box-shadow:0 12px 32px rgba(0,0,0,.22);
    transition:transform .25s ease;z-index:1050;font-size:14px;}
.sr-bulk-bar.show{transform:translateX(-50%) translateY(0);}
.sr-detail-sub{font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#8a93a6;font-weight:700;padding-top:10px !important;}
</style>

<script>
(function(){
    const selectAll = document.getElementById('selectAll');
    const rows      = () => Array.from(document.querySelectorAll('.row-check'));
    const bar       = document.getElementById('bulkBar');
    const countEl   = document.getElementById('bulkCount');
    const netEl     = document.getElementById('bulkNet');

    function refresh(){
        const checked = rows().filter(c => c.checked);
        countEl.textContent = checked.length;
        const net = checked.reduce((s,c) => s + parseFloat(c.dataset.net || 0), 0);
        netEl.textContent = net.toLocaleString('en-IN', {maximumFractionDigits:0});
        bar.classList.toggle('show', checked.length > 0);
        if (selectAll) {
            selectAll.checked = checked.length > 0 && checked.length === rows().length;
            selectAll.indeterminate = checked.length > 0 && checked.length < rows().length;
        }
    }

    if (selectAll) selectAll.addEventListener('change', () => {
        rows().forEach(c => c.checked = selectAll.checked);
        refresh();
    });
    rows().forEach(c => c.addEventListener('change', refresh));

    window.clearSelection = function(){
        rows().forEach(c => c.checked = false);
        if (selectAll){ selectAll.checked = false; selectAll.indeterminate = false; }
        refresh();
    };

    window.submitBulk = function(){
        document.getElementById('bulkReleaseForm').requestSubmit();
    };

    window.confirmBulk = function(){
        const n = rows().filter(c => c.checked).length;
        if (n === 0){ alert('Please select at least one employee.'); return false; }
        return confirm('Release payslips to ' + n + ' selected employee(s)?');
    };

    refresh();
})();

function toggleDetail(id, btn){
    const row = document.getElementById(id);
    const open = row.style.display === '';
    row.style.display = open ? 'none' : '';
    btn.classList.toggle('btn-secondary', !open);
    btn.classList.toggle('btn-outline-secondary', open);
}

function releaseOne(payslipId, name, email){
    if (!confirm('Send payslip to ' + name + ' (' + email + ')?')) return;
    const f = document.getElementById('singleReleaseForm');
    f.querySelector('[name="payslip_id"]').value = payslipId;
    f.submit();
}
</script>
@endsection