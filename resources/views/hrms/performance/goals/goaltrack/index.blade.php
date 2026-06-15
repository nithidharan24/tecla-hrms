@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Goal List');
@endphp
@extends('layouts.index')

@section('content')
<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Goal Tracking</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Goal Tracking</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{route('goal.create')}}" class="btn add-btn"> Add New</a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
    @if (Session::has('messageType') && Session::has('message'))
        @if (Session::get('messageType') === 'success')
            <div class="alert alert-success" id="success-alert">
                {{ Session::get('message') }}
            </div>
        @elseif (Session::get('messageType') === 'error')
            <div class="alert alert-danger" id="error-alert">
                {{ Session::get('message') }}
            </div>
        @endif
    @endif
        <div class="col-md-12">
          <div class="table-responsive">
    <table id="goaltrack-table" class="table custom-table datatable">
        <thead>
            <tr>
                
                <th>Goal</th>
                <th>Subject</th>
                <th>Target Achievement</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Description</th>
                <th>Status</th>
                <th>Progress</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($goalTracks as $item)
            <tr>
                <td data-label="Goal"><a class="high">{{ ucfirst($item->goal) }}</a></td>
                <td data-label="Subject">{{ $item->subject }}</td>
                <td data-label="Achievement">{{ Str::limit($item->achievement, 20) }}</td>
                <td data-label="Start Date">{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</td>
                <td data-label="End Date">{{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}</td>
                <td data-label="Description">{{ Str::limit($item->description, 30) }}</td>
                <td data-label="Status"><span class="od-chip-highlight">{{ ucfirst($item->status) }}</span></td>
                <td data-label="Progress">
                    <p class="mb-1">Completed {{ number_format($item->progress) }}%</p>
                    <div class="progress height-5">
                        <div class="progress-bar bg-primary progress-sm" style="width: {{ $item->progress }}%;"></div>
                    </div>
                </td>
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if($permissions->can_edit)
                        <a href="{{ route('goal.edit', $item->id) }}" class="od-icon-btn" title="Edit">
                            <i class="fa-solid fa-pencil"></i>
                        </a>
                        @endif
                        @if($permissions->can_delete)
                        <button class="od-icon-btn danger" title="Delete" onclick="deleteGoalTrack('{{ $item->id }}')">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                        @endif
                    </div>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Select all rows
    $('#checkAll').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    // Row highlight on individual checkbox
    $('.row-check').on('change', function() {
        $(this).closest('tr').toggleClass('od-selected', $(this).is(':checked'));
    });
});
</script>

        </div>
    </div>    

</div>
<!-- /Page Content -->

<script>

    // Automatically close the success message after 1 second
    setTimeout(function() {
        $("#success-alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 1000);

    // Automatically close the error message after 1 second
    setTimeout(function() {
        $("#error-alert").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 3000);

function statusChange(id, status) {
    Swal.fire({
        title: "Are you sure to change the status?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        allowOutsideClick: false,
    }).then(function(result) {
        if (result.isConfirmed) {
            $.ajax({
                type: "PUT",
                url: "{{ route('goaltrack-statuschange') }}",  // Updated route name
                data: { id: id, status: status },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(data) {
                if (data.status === 1) {
                    Swal.fire({
                        title: "Success",
                        text: "Goal-track status changed successfully.",
                        icon: "success"
                    }).then(()=>{
                        window.location.reload();
                    });
                }else if (data.status === 2) {
                    Swal.fire({
                        title: "Success",
                        text: "Goal-track status changed successfully.",
                        icon: "success"
                    }).then(()=>{
                        window.location.reload();
                    });
                }else {
                    Swal.fire({
                        title: "Error",
                        text: data.message,
                        icon: "error"
                    });
                }
                },
                error: function (error) {
                    console.error("Error updating Goal-track status:", error);
                    Swal.fire({
                        title: "Error",
                        text: "Failed to update Goal-track status.",
                        icon: "error"
                    });
                }
            });
        }
    });
}

function deleteGoalTrack(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/goal/' + id, // make sure your route exists
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if(response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Goal has been deleted.',
                            'success'
                        );

                        // Remove row from DataTable
                        var table = $('#goal-table').DataTable(); // replace with your table ID
                        var row = $('#goal-track-' + id);
                        table.row(row).remove().draw();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Something went wrong', 'error');
                }
            });
        }
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
