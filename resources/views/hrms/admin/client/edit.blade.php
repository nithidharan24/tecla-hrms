@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Client</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('client.index')}}">Clients</a></li>
                    <li class="breadcrumb-item active">Edit Client</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

</div>
<!-- /Page Content -->

<!-- Edit Client Form -->
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Edit Client</h5>
        </div>
        <div class="card-body">
            <form id="edit_client" method="POST" action="{{route('client.update', $client->client_id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="first_name" class="col-form-label">First Name <span class="text-danger">*</span></label>
                            <input id="first_name" name="first_name" class="form-control" type="text" value="{{ old('first_name', $client->first_name) }}" required />
                            <div class="invalid-feedback">Please enter first name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="last_name" class="col-form-label">Last Name <span class="text-danger">*</span></label>
                            <input id="last_name" name="last_name" class="form-control" type="text" value="{{ old('last_name', $client->last_name) }}" required />
                            <div class="invalid-feedback">Please enter last name.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="username" class="col-form-label">Username <span class="text-danger">*</span></label>
                            <input id="username" name="username" class="form-control" type="text" value="{{ old('username', $client->user_name) }}" required />
                            <div class="invalid-feedback">Please enter username.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="email" class="col-form-label">Email <span class="text-danger">*</span></label>
                            <input id="email" name="email" class="form-control" type="email" value="{{ old('email', $client->email) }}" required />
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="password" class="col-form-label">Password</label>
                            <input id="password" name="password" class="form-control" type="password" placeholder="Leave blank to keep current password" />
                            <div class="invalid-feedback">Please enter a valid password (min 8 characters).</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="phone" class="col-form-label">Phone <span class="text-danger">*</span></label>
                            <input id="phone" name="phone" class="form-control" type="text"
                                value="{{ old('phone', $client->phone) }}"
                                pattern="[6-9]{1}[0-9]{9}"
                                minlength="10"
                                maxlength="10"
                                required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            />
                            <div class="invalid-feedback">Please enter a valid 10-digit phone number starting with 6-9.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="company_name" class="col-form-label">Company Name <span class="text-danger">*</span></label>
                            <input id="company_name" name="company_name" class="form-control" type="text" value="{{ old('company_name', $client->company_name) }}" required />
                            <div class="invalid-feedback">Please enter company name.</div>
                        </div>
                    </div>
                    
                    <!-- Website URL -->
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="website_url" class="col-form-label">Website URL</label>
                            <input id="website_url" name="website_url" class="form-control" type="url" value="{{ old('website_url', $client->website_url) }}" placeholder="https://example.com" />
                            <div class="invalid-feedback">Please enter a valid URL.</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="services" class="col-form-label">Services <span class="text-danger">*</span></label>
                            <select name="services[]" id="services" class="form-control select2" multiple required>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" 
                                        {{ in_array($service->id, $clientServices) ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select at least one service.</div>
                        </div>
                    </div>
                    
                    <!-- Live URL -->
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="live_url" class="col-form-label">Live URL</label>
                            <input id="live_url" name="live_url" class="form-control" type="url" value="{{ old('live_url', $client->live_url) }}" placeholder="https://example.com" />
                            <div class="invalid-feedback">Please enter a valid URL.</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="input-block mb-3">
                            <label for="address" class="col-form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="client_address" id="address" class="form-control" style="resize: none;" rows="4" required>{{ old('client_address', $client->address) }}</textarea>
                            <div class="invalid-feedback">Please enter address.</div>
                        </div>
                    </div>
                </div>

                <!-- Section: Hosting Information -->
                <h6 class="mb-3 text-primary">Hosting Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="hosting_type" class="col-form-label">Hosting Type <span class="text-danger">*</span></label>
                            <select name="hosting_type" id="hosting_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="hosting_with_us" {{ old('hosting_type', $client->hosting_type) == 'hosting_with_us' ? 'selected' : '' }}>Hosting with us</option>
                                <option value="their_hosting" {{ old('hosting_type', $client->hosting_type) == 'their_hosting' ? 'selected' : '' }}>Their Hosting</option>
                                <option value="hosting_maintenance_with_us" {{ old('hosting_type', $client->hosting_type) == 'hosting_maintenance_with_us' ? 'selected' : '' }}>Hosting & Maintenance with us</option>
                            </select>
                            <div class="invalid-feedback">Please select hosting type.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="hosting_status" class="col-form-label">Hosting Status <span class="text-danger">*</span></label>
                            <select name="hosting_status" id="hosting_status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('hosting_status', $client->hosting_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('hosting_status', $client->hosting_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ old('hosting_status', $client->hosting_status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                            <div class="invalid-feedback">Please select hosting status.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="hosting_expiry_date" class="col-form-label">Hosting Expiry Date</label>
                            <input id="hosting_expiry_date" name="hosting_expiry_date" class="form-control" type="date" value="{{ old('hosting_expiry_date', $client->hosting_expiry_date) }}" />
                        </div>
                    </div>
                </div>

                <!-- Section: Domain Information -->
                <h6 class="mb-3 text-primary">Domain Information</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="domain_type" class="col-form-label">Domain Type <span class="text-danger">*</span></label>
                            <select name="domain_type" id="domain_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="domain_with_us" {{ old('domain_type', $client->domain_type) == 'domain_with_us' ? 'selected' : '' }}>Domain with us</option>
                                <option value="their_domain" {{ old('domain_type', $client->domain_type) == 'their_domain' ? 'selected' : '' }}>Their Domain</option>
                                <option value="domain_maintenance_with_us" {{ old('domain_type', $client->domain_type) == 'domain_maintenance_with_us' ? 'selected' : '' }}>Domain & Maintenance with us</option>
                            </select>
                            <div class="invalid-feedback">Please select domain type.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="domain_status" class="col-form-label">Domain Status <span class="text-danger">*</span></label>
                            <select name="domain_status" id="domain_status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="active" {{ old('domain_status', $client->domain_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('domain_status', $client->domain_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ old('domain_status', $client->domain_status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                            <div class="invalid-feedback">Please select domain status.</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-block mb-3">
                            <label for="domain_expiry_date" class="col-form-label">Domain Expiry Date</label>
                            <input id="domain_expiry_date" name="domain_expiry_date" class="form-control" type="date" value="{{ old('domain_expiry_date', $client->domain_expiry_date) }}" />
                        </div>
                    </div>
                </div>

                <!-- Section: Digital Marketing Information -->
                <h6 class="mb-3 text-primary">Digital Marketing Information</h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="digital_marketing_start_date" class="col-form-label">Service Start Date</label>
                            <input id="digital_marketing_start_date" name="digital_marketing_start_date" class="form-control" type="date" value="{{ old('digital_marketing_start_date', $client->digital_marketing_start_date) }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="digital_marketing_end_date" class="col-form-label">Service End Date</label>
                            <input id="digital_marketing_end_date" name="digital_marketing_end_date" class="form-control" type="date" value="{{ old('digital_marketing_end_date', $client->digital_marketing_end_date) }}" />
                        </div>
                    </div>
                </div>

                <!-- AMC Renewal Date -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="hosting_amc_renewal_date" class="col-form-label">Hosting AMC Renewal Date</label>
                            <input id="hosting_amc_renewal_date" name="hosting_amc_renewal_date" class="form-control" type="date" value="{{ old('hosting_amc_renewal_date', $client->hosting_amc_renewal_date) }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-block mb-3">
                            <label for="amc_reminder_days" class="col-form-label">AMC Reminder Days</label>
                            <input id="amc_reminder_days" name="amc_reminder_days" class="form-control" type="number" min="1" max="365" value="{{ old('amc_reminder_days', $client->amc_reminder_days ?? 30) }}" placeholder="Days before expiry to send reminder" />
                            <small class="form-text text-muted">Number of days before AMC expiry to send reminder</small>
                        </div>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" id="submitbtn" class="btn btn-primary submit-btn">Update Client</button>
                    <a href="{{ route('client.index') }}" class="btn btn-secondary submit-btn px-5 ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Jquery Validation
    $(document).ready(function () {
        // Custom validation method for "First Name"
        $.validator.addMethod("customName", function (value, element) {
            return this.optional(element) || /^[^\s].{0,29}$/.test(value);
        }, "Invalid name format. No leading spaces allowed, and must not exceed 30 characters.");

        // Form validation with new fields
        $("#edit_client").validate({
            rules: {
                first_name: {
                    required: true,
                    customName: true
                },
                last_name: {
                    required: true
                },
                username: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    minlength: 8
                },
                phone: {
                    required: true,
                },
                company_name: {
                    required: true,
                    maxlength: 50
                },
                client_address: {
                    required: true,
                    maxlength: 300
                },
                website_url: {
                    url: true
                },
                live_url: {
                    url: true
                },
                hosting_type: {
                    required: true
                },
                hosting_status: {
                    required: true
                },
                domain_type: {
                    required: true
                },
                domain_status: {
                    required: true
                }
            },
            messages: {
                first_name: {
                    required: "First Name is required."
                },
                last_name: {
                    required: "Last Name is required."
                },
                username: {
                    required: "Username is required."
                },
                email: {
                    required: "Email is required.",
                    email: "Please enter a valid email address."
                },
                password: {
                    minlength: "Password must be at least 8 characters long."
                },
                phone: {
                    required: "Phone number is required.",
                },
                company_name: {
                    required: "Company Name is required.",
                    maxlength: "Company Name must not exceed 50 characters."
                },
                client_address: {
                    required: "Address is required.",
                    maxlength: "Address must not exceed 300 characters."
                },
                hosting_type: {
                    required: "Hosting Type is required."
                },
                hosting_status: {
                    required: "Hosting Status is required."
                },
                domain_type: {
                    required: "Domain Type is required."
                },
                domain_status: {
                    required: "Domain Status is required."
                }
            },
            errorClass: "error",
            submitHandler: function (form) {
                // Show loading state
                $('#submitbtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating Client...');
                form.submit();
            }
        });

        // Bootstrap validation
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Auto-set Digital Marketing end date based on start date (1 year later)
        $('#digital_marketing_start_date').change(function() {
            if ($(this).val()) {
                var startDate = new Date($(this).val());
                var endDate = new Date(startDate);
                endDate.setFullYear(endDate.getFullYear() + 1);
                
                var formattedEndDate = endDate.toISOString().split('T')[0];
                $('#digital_marketing_end_date').val(formattedEndDate);
            }
        });
    });

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

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select services",
            allowClear: true
        });
    });
</script>

@endsection