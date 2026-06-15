@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Provident Fund</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('providentfund.index')}}">Provident Fund</a></li>
                    <li class="breadcrumb-item active">Edit Provident Fund</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="form-section">
                <h5 class="form-title">Edit Provident Fund</h5>
                <form id="pf-form" method="POST" action="{{route('providentfund.update',$pf_fund->pf_id)}}" novalidate> 
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Employee Name</label>
                                <select id="employee-name" name="employee_name" class="form-control select">
                                    <option value="">Select an Employee</option>
                                    @if (count($employees)>0)
                                        @foreach ($employees as $item)
                                            <option value="{{$item->employeeid}}" {{$pf_fund->employee_name == $item->employeeid ? 'selected' : ''}}>{{UcFirst($item->firstname).' '.$item->lastname}} ({{$item->employeeid}})</option>
                                        @endforeach
                                    @else
                                        <option>No Options Found</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Provident Fund Type</label>
                                <select class="form-control select" onchange="handlePFTypeChange()" name="pf_type" id="pf-type">
                                    <option value="">Select a PF Type</option>
                                    <option value="Fixed Amount" {{$pf_fund->pf_type=='Fixed Amount' ? 'selected' : ''}}>Fixed Amount</option>
                                    <option value="Percentage Based" {{$pf_fund->pf_type=='Percentage Based' ? 'selected' : ''}}>Percentage Based</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="show-fixed-amount">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Employee Share (Amount)</label>
                                            <input class="form-control" name="employee_share_amount" value="{{ old('employee_share_amount', $pf_fund->pf_type == 'Fixed Amount' ? $pf_fund->employee_share_amount : '') }}" id="employee-share-amount" type="text" {{ $pf_fund->pf_type == 'Percentage Based' ? 'readonly' : '' }}>

                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Organization Share (Amount)</label>
                                            <input class="form-control" id="organization-share-amount" value="{{ old('organization_share_amount', $pf_fund->pf_type == 'Fixed Amount' ? $pf_fund->organization_share_amount : '') }}" name="organization_share_amount" type="text" {{$pf_fund->pf_type=='Percentage Based' ? 'readonly' : ''}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="show-basic-salary">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Employee Share (%)</label>
                                            <input class="form-control" name="employee_share_percent" value="{{ old('employee_share_percent', $pf_fund->pf_type == 'Percentage Based' ? $pf_fund->employee_share_percent : '') }}" id="employee-share-percent" type="text" {{$pf_fund->pf_type=='Fixed Amount' ? 'readonly' : ''}}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Organization Share (%)</label>
                                            <input class="form-control" name="organization_share_percent" value="{{ old('organization_share_percent', $pf_fund->pf_type == 'Percentage Based' ? $pf_fund->organization_share_percent : '') }}" id="organization-share-percent" type="text" {{$pf_fund->pf_type=='Fixed Amount' ? 'readonly' : ''}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" style="resize: none;">{{old('description',$pf_fund->description)}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

</div>
<!-- /Page Content -->
<script>
$(document).ready(function() {
    $("#pf-form").validate({
        rules: {
            employee_name: {
                required: true
            },
            pf_type: {
                required: true
            },
            employee_share_amount: {
                required: function(element) {
                    return $('#pf-type').val() === 'Fixed Amount';
                }
            },
            organization_share_amount: {
                required: function(element) {
                    return $('#pf-type').val() === 'Fixed Amount';
                }
            },
            employee_share_percent: {
                required: function(element) {
                    return $('#pf-type').val() === 'Percentage Based';
                }
            },
            organization_share_percent: {
                required: function(element) {
                    return $('#pf-type').val() === 'Percentage Based';
                }
            },
            description:{
                required: true,
                maxlength:300,
            }
        },
        messages: {
            employee_name: "Please select an employee.",
            pf_type: "Please select a provident fund type.",
            employee_share_amount: "Employee share amount is required.",
            organization_share_amount: "Organization share amount is required.",
            employee_share_percent: "Employee share percentage is required.",
            organization_share_percent: "Organization share percentage is required.",
            description:{
                required: "Description is required",
                maxlength: "Description must not exceed 300 characters",
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.input-block').append(error);
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});

function handlePFTypeChange() {
    const pfTypeDropdown = document.getElementById('pf-type');

    const amountFields = [
        document.getElementById('employee-share-amount'),
        document.getElementById('organization-share-amount')
    ];
    const percentFields = [
        document.getElementById('employee-share-percent'),
        document.getElementById('organization-share-percent')
    ];

    const isFixed = pfTypeDropdown.value === 'Fixed Amount';

    amountFields.forEach(field => {
        field.readOnly = !isFixed;
        if (!isFixed) field.value = '';
    });
    
    percentFields.forEach(field => {
        field.readOnly = isFixed;
        if (isFixed) field.value = '';
    });
}

    // Show validation errors using SweetAlert
@if ($errors->any())
    var errorMessage = '';
@foreach ($errors->all() as $error)
    errorMessage += "{{ $error }}\n";
@endforeach
    Swal.fire({
        icon: 'error',
        title: 'Validation Errors',
        text: errorMessage,
});
@endif
</script>

@endsection
