@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header bg-light">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title d-flex align-items-center">Performance Review</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="">Performance</a></li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('performance-review.create') }}" class="btn btn-primary">
                     Add New</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if(Session::has('success'))
        <div class="alert alert-success" id="success-alert">{{ Session::get('success') }}</div>
    @elseif (Session::has('error'))
        <div class="alert alert-danger" id="error-alert">{{ Session::get('error') }}</div>
    @endif

    <div id="status-update-alert"></div>

<!-- Export Buttons -->
{{-- <div class="row mb-4">
    <div class="col-md-12">
        <div class="text-end">
            <a href="{{ route('performance-review.export-pdf', $review->id) }}" class="btn btn-primary me-2">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('performance-review.export-excel', $review->id) }}" class="btn btn-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>
</div> --}}
<!-- /Export Buttons -->
    
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table datatable">
                    <thead>
                        <tr>
                            <th class="text-center">S.I</th>
                            <th class="text-center">Employee</th>
                            <th class="text-center">Designation</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Appraisal Date</th>
                            <th class="text-center">Appraisal Update Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($performance_review_basic_infos as $performance_review_basic_info)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td  class="text-center">{{ $performance_review_basic_info->firstname ?? 'firstname' }} {{ $performance_review_basic_info->lastname ?? 'lastname' }}</td> 
                            <td  class="text-center">{{ $performance_review_basic_info->designation ?? 'Role' }}</td> 
                            <td class="text-center">{{ $performance_review_basic_info->department_name ?? 'Department'  }}</td> 
                            <td class="text-center">{{ \Carbon\Carbon::parse($performance_review_basic_info->created_at)->format('d-m-Y') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($performance_review_basic_info->updated_at)->format('d-m-Y') }}</td>
                            <td>
                                <div class="dropdown action-label">
                                    <a href="#" class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fa-regular fa-circle-dot {{ $performance_review_basic_info->status == 'active' ? 'text-success' : 'text-danger' }}"></i>
                                        {{ ucfirst($performance_review_basic_info->status) }}
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item update-status" href="#" data-id="{{ $performance_review_basic_info->id }}" data-status="active">
                                            <i class="fa-regular fa-circle-dot text-success"></i> Active
                                        </a>
                                        <a class="dropdown-item update-status" href="#" data-id="{{ $performance_review_basic_info->id }}" data-status="inactive">
                                            <i class="fa-regular fa-circle-dot text-danger"></i> Inactive
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item"  href="{{ route('performance-review.edit', $performance_review_basic_info->id) }}"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
                                        <button type="button" class="dropdown-item delete-btn" data-id="{{ $performance_review_basic_info->id }}">
                                            <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                        </button>                                     
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /Page Content -->

<!-- Delete Today Work Modal -->
<div class="modal custom-modal fade" id="delete_workdetail" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Performance Review Details</h3>
                    <p>Are you sure you want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <div class="row">
                        <div class="col-6">
                            <a href="javascript:void(0);" id="confirm-delete" class="btn btn-primary continue-btn">Delete</a>
                        </div>
                        <div class="col-6">
                            <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary cancel-btn">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Delete Modal -->

<script>
    // Automatically close success and error messages
    setTimeout(function() {
        $("#success-alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 1000);

    setTimeout(function() {
        $("#error-alert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 3000);

      // Handle status updates
      $(document).on('click', '.update-status', function (e) {
        e.preventDefault();

        let performance_review_basic_infoId = $(this).data('id');
        let newStatus = $(this).data('status');
        let $statusDropdown = $(this).closest('.dropdown');

        $.ajax({
            url: '/performance-review/' + performance_review_basic_infoId  + '/update-status',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function (response) {
                if (response.success) {
                    // Update the dropdown text and icon color
                    let iconClass = newStatus == 'active' ? 'text-success' : 'text-danger';
                    $statusDropdown.find('.dropdown-toggle').html(`
                        <i class="fa-regular fa-circle-dot ${iconClass}"></i> ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
                    `);
                    // Show success alert
                    $("#status-update-alert").html(`
                        <div class="alert alert-success">
                            Status updated successfully!
                        </div>
                    `).fadeIn().delay(3000).fadeOut();
                } else {
                    $("#status-update-alert").html(`
                        <div class="alert alert-danger">
                            ${response.message}
                        </div>
                    `).fadeIn().delay(3000).fadeOut();
                }
            },
            error: function () {
                $("#status-update-alert").html(`
                    <div class="alert alert-danger">
                        Error updating status. Please try again later.
                    </div>
                `).fadeIn().delay(3000).fadeOut();
            }
        });
    });

    // Delete confirmation modal logic
    let deleteForm;
    $('body').on('click', '.delete-btn', function() {
        var id = $(this).data('id');
        deleteForm = $(`<form action="{{ url('performance-review') }}/${id}" method="POST">
                            @csrf
                            @method('DELETE')
                        </form>`);
        $('body').append(deleteForm);
        $('#delete_workdetail').modal('show');
    });

    // Confirm deletion
    $('#confirm-delete').on('click', function() {
        deleteForm.submit();
    });

</script>

@endsection