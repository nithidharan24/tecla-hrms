@extends('layouts.index')

@section('content')
<div class="container-fluid mt-5">
    <div class="content">
        <div class="card">
            <div class="card-header">
                <h4>Memo Send Details</h4>
                <a href="{{ route('memo.history') }}" class="btn btn-secondary btn-sm float-end">Back to History</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Employee Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $sendRecord->employee_name }}</td>
                            </tr>
                            <tr>
                                <th>Employee ID:</th>
                                <td>{{ $sendRecord->employee_employeeid }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $sendRecord->employee_email }}</td>
                            </tr>
                            <tr>
                                <th>Designation:</th>
                                <td>{{ $sendRecord->designation_name }}</td>
                            </tr>
                            <tr>
                                <th>Department:</th>
                                <td>{{ $sendRecord->department_name }}</td>
                            </tr>
                            <tr>
                                <th>Branch:</th>
                                <td>{{ $sendRecord->branch_name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Memo Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Memo Template:</th>
                                <td>{{ $sendRecord->memo_name }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($sendRecord->status == 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Sent At:</th>
                                <td>{{ $sendRecord->sent_at ? date('d-m-Y H:i:s', strtotime($sendRecord->sent_at)) : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ date('d-m-Y H:i:s', strtotime($sendRecord->created_at)) }}</td>
                            </tr>
                            @if($sendRecord->error_message)
                            <tr>
                                <th>Error Message:</th>
                                <td class="text-danger">{{ $sendRecord->error_message }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h5>Memo Content</h5>
                        <div class="card">
                            <div class="card-body">
                                {!! nl2br(e($sendRecord->memo_content)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection