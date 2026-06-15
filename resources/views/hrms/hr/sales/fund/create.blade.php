@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Provident Fund</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('providentfund.index')}}">Provident Fund</a></li>
                    <li class="breadcrumb-item active">Create Provident Fund</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="form-section">
                <h5 class="form-title">Add Provident Fund</h5>
                <form id="pf-form" method="POST" action="{{route('providentfund.store')}}" novalidate> 
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Employee Name</label>
                                <select id="employee-name" name="employee_name" class="form-control select">
                                    <option value="">Select an Employee</option>
                                    @if (count($employees)>0)
                                        @foreach ($employees as $item)
                                            <option value="{{$item->employeeid}}">{{UcFirst($item->firstname).' '.$item->lastname}} ({{$item->employeeid}})</option>
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
                                    <option value="Fixed Amount">Fixed Amount</option>
                                    <option value="Percentage Based">Percentage Based</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="show-fixed-amount">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Employee Share (Amount)</label>
                                            <input class="form-control restricted-input" name="employee_share_amount" id="employee-share-amount" type="text">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Organization Share (Amount)</label>
                                            <input class="form-control restricted-input" id="organization-share-amount" name="organization_share_amount" type="text">
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
                                            <input class="form-control restricted-input" name="employee_share_percent" id="employee-share-percent" type="text">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="input-block mb-3">
                                            <label class="col-form-label">Organization Share (%)</label>
                                            <input class="form-control restricted-input" name="organization_share_percent" id="organization-share-percent" type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="input-block mb-3">
                                <label class="col-form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" style="resize: none;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="button" class="btn btn-primary submit-btn restricted-btn" id="submit-button">
                            <span class="btn-text">Submit</span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                       
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Page Content -->

<style>
.restricted-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.restricted-input:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-processing {
    pointer-events: none;
    opacity: 0.7;
}
</style>

<script>
$(document).ready(function() {
    // Initialize form validation
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
        }
    });

    // Button restriction and management
    initializeButtonRestrictions();
    
    // Form change monitoring
    $('#pf-form input, #pf-form select, #pf-form textarea').on('change keyup', function() {
        updateButtonStates();
    });
});

function initializeButtonRestrictions() {
    // Prevent all restricted buttons from default behavior when disabled
    $('.restricted-btn').on('click', function(e) {
        if ($(this).prop('disabled') || $(this).hasClass('btn-processing')) {
            e.preventDefault();
            e.stopPropagation();
            showRestrictedMessage();
            return false;
        }
    });

    // Submit button handler
    $('#submit-button').on('click', function(e) {
        e.preventDefault();
        if (!$(this).prop('disabled') && !$(this).hasClass('btn-processing')) {
            handleFormSubmission();
        }
    });

    // Reset button handler
    $('#reset-button').on('click', function(e) {
        e.preventDefault();
        if (!$(this).prop('disabled')) {
            resetForm();
        }
    });

    // Preview button handler
    $('#preview-button').on('click', function(e) {
        e.preventDefault();
        if (!$(this).prop('disabled')) {
            previewFormData();
        }
    });
}

function updateButtonStates() {
    const form = $('#pf-form');
    const isFormValid = form.valid();
    const hasRequiredFields = checkRequiredFields();
    
    // Enable/disable submit button
    $('#submit-button').prop('disabled', !isFormValid || !hasRequiredFields);
    
    // Enable preview button if form has some data
    const hasAnyData = form.find('input, select, textarea').filter(function() {
        return $(this).val() !== '';
    }).length > 0;
    
    $('#preview-button').prop('disabled', !hasAnyData);
}

function checkRequiredFields() {
    const employeeName = $('#employee-name').val();
    const pfType = $('#pf-type').val();
    const description = $('#description').val();
    
    if (!employeeName || !pfType || !description) {
        return false;
    }
    
    if (pfType === 'Fixed Amount') {
        return $('#employee-share-amount').val() && $('#organization-share-amount').val();
    } else if (pfType === 'Percentage Based') {
        return $('#employee-share-percent').val() && $('#organization-share-percent').val();
    }
    
    return true;
}

function handleFormSubmission() {
    const submitBtn = $('#submit-button');
    
    if ($('#pf-form').valid()) {
        // Show processing state
        submitBtn.addClass('btn-processing');
        submitBtn.find('.btn-text').hide();
        submitBtn.find('.btn-loader').show();
        
        // Disable all buttons during submission
        $('.restricted-btn').prop('disabled', true);
        
        // Submit the form
        setTimeout(() => {
            $('#pf-form')[0].submit();
        }, 500);
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Form Validation',
            text: 'Please fill in all required fields correctly.',
        });
    }
}

function resetForm() {
    Swal.fire({
        title: 'Reset Form?',
        text: "This will clear all entered data. Are you sure?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#pf-form')[0].reset();
            $('.restricted-input').prop('disabled', false).prop('readonly', false);
            updateButtonStates();
            
            Swal.fire(
                'Reset!',
                'Form has been reset successfully.',
                'success'
            );
        }
    });
}

function previewFormData() {
    const formData = {
        employee_name: $('#employee-name option:selected').text(),
        pf_type: $('#pf-type').val(),
        employee_share_amount: $('#employee-share-amount').val(),
        organization_share_amount: $('#organization-share-amount').val(),
        employee_share_percent: $('#employee-share-percent').val(),
        organization_share_percent: $('#organization-share-percent').val(),
        description: $('#description').val()
    };
    
    let previewHtml = '<div class="text-left">';
    previewHtml += `<p><strong>Employee:</strong> ${formData.employee_name}</p>`;
    previewHtml += `<p><strong>PF Type:</strong> ${formData.pf_type}</p>`;
    
    if (formData.pf_type === 'Fixed Amount') {
        previewHtml += `<p><strong>Employee Share:</strong> $${formData.employee_share_amount}</p>`;
        previewHtml += `<p><strong>Organization Share:</strong> $${formData.organization_share_amount}</p>`;
    } else if (formData.pf_type === 'Percentage Based') {
        previewHtml += `<p><strong>Employee Share:</strong> ${formData.employee_share_percent}%</p>`;
        previewHtml += `<p><strong>Organization Share:</strong> ${formData.organization_share_percent}%</p>`;
    }
    
    previewHtml += `<p><strong>Description:</strong> ${formData.description}</p>`;
    previewHtml += '</div>';
    
    Swal.fire({
        title: 'Form Preview',
        html: previewHtml,
        icon: 'info',
        width: '600px'
    });
}

function showRestrictedMessage() {
    Swal.fire({
        icon: 'warning',
        title: 'Action Restricted',
        text: 'This button is currently disabled. Please complete the required fields first.',
        timer: 2000,
        showConfirmButton: false
    });
}

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
        field.disabled = !isFixed;
        if (!isFixed) field.value = '';
    });
    
    percentFields.forEach(field => {
        field.readOnly = isFixed;
        field.disabled = isFixed;
        if (isFixed) field.value = '';
    });
    
    // Update button states after field changes
    updateButtonStates();
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