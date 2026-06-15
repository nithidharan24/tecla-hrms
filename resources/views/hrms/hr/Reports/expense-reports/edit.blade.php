@extends('layouts.index')

@section('content')
<div class="page-wrapper" style="margin-left: 5px; padding-top: 10px;">
    <div class="content container-fluid" style="max-width: 100%; padding-top: 10px;">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Edit Expense</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('expenses-reports.index') }}">Expense Report</a></li>
                        <li class="breadcrumb-item active">Edit Expense</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('expenses-reports.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Item Name</label>
                                        <input class="form-control" name="item_name" value="{{ $expense->item_name }}" type="text" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Purchase From</label>
                                        <input class="form-control" name="purchase_from" value="{{ $expense->purchase_from }}" type="text" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
    <div class="input-block mb-3">
        <label class="col-form-label">Purchase Date</label>
        <div class="cal-icon">
            <input class="form-control datetimepicker" 
                   name="purchase_date" 
                   value="{{ $expense->purchase_date }}" 
                   required>
        </div>
    </div>
</div>

                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Purchased By</label>
                                        <select class="form-control" name="purchased_by" required>
                                            <option value="{{ $expense->purchased_by }}" selected>{{ $expense->purchased_by }}</option>
                                            <option value="Daniel Porter">Daniel Porter</option>
                                            <option value="Roger Dixon">Roger Dixon</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Amount</label>
                                        <input class="form-control" name="amount" value="{{ $expense->amount }}" type="number" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Paid By</label>
                                        <select class="form-control" name="paid_by" required>
                                            <option value="{{ $expense->paid_by }}" selected>{{ $expense->paid_by }}</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Cheque">Cheque</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-block mb-3">
                                        <label class="col-form-label">Status</label>
                                        <select class="form-control" name="status" required>
                                            <option value="pending" {{ $expense->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $expense->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                </div>
                             
<div class="row">
    <div class="col-md-6">
        <div class="input-block mb-3">
            <label class="col-form-label">Attachments</label>
            <input class="form-control" name="attachments[]" type="file" multiple>
            
            <!-- Display existing attachments -->
            @if ($expense->attachments)
                <div class="existing-attachments mt-3">
                    <h6>Current Attachments:</h6>
                    <ul class="list-unstyled">
                        @php
                            $attachments = explode(',', $expense->attachments);
                        @endphp
                        
                        @foreach ($attachments as $attachment)
                            @php
                                $fileName = basename($attachment);
                            @endphp
                            <li class="mb-2">
                                <div class="d-flex align-items-center">
                                    @if(in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset('storage/' . $attachment) }}" alt="{{ $fileName }}" 
                                             style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                    @else
                                        <i class="far fa-file-alt fa-2x" style="margin-right: 10px;"></i>
                                    @endif
                                    
                                    <div>
                                        <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                           class="text-primary" title="Download">
                                            {{ $fileName }}
                                        </a>
                                        <br>
                                       
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

                            <div class="submit-section">
                                <button class="btn btn-primary submit-btn" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
