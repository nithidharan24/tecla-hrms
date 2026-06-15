<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { font-family: DejaVu Sans, sans-serif; }
    body { font-size: 12px; color: #222; }
    .head { text-align:center; border-bottom:2px solid #4a3aff; padding-bottom:8px; margin-bottom:14px; }
    .head h2 { margin:0; color:#4a3aff; }
    .head small { color:#666; }
    .meta { width:100%; margin-bottom:14px; }
    .meta td { padding:3px 0; }
    table.sal { width:100%; border-collapse:collapse; margin-top:8px; }
    table.sal th, table.sal td { border:1px solid #ddd; padding:7px 10px; }
    table.sal th { background:#f4f3ff; text-align:left; color:#4a3aff; }
    .amt { text-align:right; }
    .tot td { font-weight:bold; background:#fafafa; }
    .net { margin-top:14px; padding:10px; background:#f4f3ff; border-radius:6px; }
    .net b { color:#4a3aff; }
    .words { font-style:italic; color:#555; margin-top:6px; }
    .foot { margin-top:24px; font-size:10px; color:#888; text-align:center; }
</style>
</head>
<body>
    <div class="head">
        @if(!empty($company['logoPath']))
            <img src="{{ $company['logoPath'] }}" style="height:46px;"><br>
        @endif
        <h2>{{ $company['name'] }}</h2>
        <small>{{ $company['address'] }} @if($company['phone']) | {{ $company['phone'] }} @endif</small>
        <h3 style="margin:10px 0 0;">Payslip — {{ $salaryMonth }}</h3>
    </div>

    <table class="meta">
        <tr>
            <td><b>Employee:</b> {{ $employee->firstname }} {{ $employee->lastname }}</td>
            <td><b>Employee ID:</b> {{ $employee->employeeid }}</td>
        </tr>
        <tr>
            <td><b>Designation:</b> {{ $employee->designation_name ?? '-' }}</td>
            <td><b>Department:</b> {{ $employee->department_name ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Working Days:</b> {{ $payslip->actual_working_days ?? 0 }}/{{ $payslip->total_working_days ?? 0 }}</td>
            <td><b>LOP Days:</b> {{ $payslip->lop_days ?? 0 }}</td>
        </tr>
    </table>

    <table class="sal">
        <tr>
            <th colspan="2">Earnings</th>
            <th colspan="2">Deductions</th>
        </tr>
        @php $rows = max(count($earnings), count($deductions)); @endphp
        @for($i = 0; $i < $rows; $i++)
        <tr>
            <td>{{ $earnings[$i]['label'] ?? '' }}</td>
            <td class="amt">{{ isset($earnings[$i]) ? '₹'.number_format($earnings[$i]['amount'],2) : '' }}</td>
            <td>{{ $deductions[$i]['label'] ?? '' }}</td>
            <td class="amt">{{ isset($deductions[$i]) ? '₹'.number_format($deductions[$i]['amount'],2) : '' }}</td>
        </tr>
        @endfor
        <tr class="tot">
            <td>Total Earnings</td>
            <td class="amt">₹{{ number_format($totalEarnings,2) }}</td>
            <td>Total Deductions</td>
            <td class="amt">₹{{ number_format($totalDeductions,2) }}</td>
        </tr>
    </table>

    <div class="net">
        <b>Net Pay: ₹{{ number_format($netSalary,2) }}</b>
        <div class="words">In words: {{ $netInWords }}</div>
    </div>

    <div class="foot">This is a system-generated payslip and does not require a signature.</div>
</body>
</html>