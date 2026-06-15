@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title">Holidays</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Holidays</li>
                    </ul>
                </div>
                <div>
                    <!-- Add Holiday Button -->
                    @if(isset($permissions) && $permissions->can_create)
                    <a href="#" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addHolidayModal">
                         Add Holiday
                    </a>
                    @endif
                </div>
            </div>

            <!-- Display Success Message -->
            @if(session('success'))
                <div class="alert alert-success" id="success-alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Display Validation Errors -->
            @if ($errors->any())
                @if ($errors->has('holiday_date'))
                    <div class="alert alert-danger" id="holiday-date-alert">
                        {{ $errors->first('holiday_date') }}
                    </div>
                @endif
            @endif

            <!-- Holidays Table -->
           <div class="table-responsive">
    <table class="table custom-table datatable mb-0">
        <thead>
            <tr>
               
                <th>S.No</th>
                <th>Title</th>
                <th>Holiday Date</th>
                <th>Day</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @foreach($holidays->sortBy('holidaydate') as $holiday)
            <tr>
               
                <td data-label="S. No.">{{ $counter++ }}</td>

<td data-label="Holiday Title">
    <span class="od-chip-highlight">{{ $holiday->title }}</span>
</td>

<td data-label="Date">
    <span class="high">{{ \Carbon\Carbon::parse($holiday->holidaydate)->format('d M Y') }}</span>
</td>

<td data-label="Day">
    {{ \Carbon\Carbon::parse($holiday->holidaydate)->format('l') }}
</td>
<td data-label="Actions" class="text-center">
    <div class="od-inline-actions">
        @if(session('role') === 'admin' || (isset($permissions) && $permissions->can_edit))
        <button class="od-icon-btn" title="Edit" onclick="editHoliday({{ $holiday->id }})">
            <i class="fa-solid fa-pencil"></i>
        </button>
        @endif

        @if(session('role') === 'admin' || (isset($permissions) && $permissions->can_delete))
        <button class="od-icon-btn danger" title="Delete" onclick="confirmDelete({{ $holiday->id }})">
            <i class="fa-solid fa-trash"></i>
        </button>
        @endif
    </div>
</td>




            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllHolidays = document.getElementById('checkAllHolidays');
const rowChecksHoliday = document.querySelectorAll('.row-check-holiday');

checkAllHolidays?.addEventListener('change', function() {
    rowChecksHoliday.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksHoliday.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

        </div>
    </div>
</div>

<!-- Add/Edit Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" role="dialog" aria-labelledby="addHolidayModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHolidayModalLabel">Add Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                    style="background-color: orange; border-radius: 50%; width: 30px; height: 30px; color: white; font-size: 16px; line-height: 30px; text-align: center;">
                    &times;
                </button>
            </div>
            <div class="modal-body">
                <form id="holidayForm" action="{{ route('holidays.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="holiday-id" name="holiday_id">
                    <div class="form-group mb-3">
                        <label for="holiday-title">Holiday Title *</label>
                        <input type="text" class="form-control" id="holiday-title" name="holiday_title" required>
                        <div id="title-warning" class="alert alert-warning mt-2" style="display: none;">Holiday title already exists!</div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="holiday-date">Holiday Date *</label>
                        <input type="date" class="form-control" id="holiday-date" name="holiday_date" required>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
// Hide success alert after 2 seconds
$("#success-alert").fadeTo(2000, 500).slideUp(500, function() {
    $("#success-alert").slideUp(500);
});

// Hide holiday date alert after 2 seconds (if it exists)
if ($("#holiday-date-alert").length) {
    $("#holiday-date-alert").fadeTo(2000, 500).slideUp(500, function() {
        $("#holiday-date-alert").slideUp(500);
    });
}

// Function for delete confirmation with SweetAlert
function confirmDelete(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This holiday will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Perform the delete action
            $.ajax({
                url: '{{ route("holidays.destroy", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                }
            });
        }
    });
}

function editHoliday(id) {
    // Fetch the holiday data using AJAX
    $.get('{{ route("holidays.edit", ":id") }}'.replace(':id', id), function(holiday) {
        // Set the modal form action to update
           $('#holidayForm').attr('action', '{{ route("holidays.update", "") }}/' + id);
        
        // Set the form fields with the holiday data
        $('#holiday-id').val(holiday.id);
        $('#holiday-title').val(holiday.title);
        $('#holiday-date').val(holiday.holidaydate);

        // Change the modal title and button text
        $('#addHolidayModalLabel').text('Edit Holiday');
        $('#submitBtn').text('Update');

        // Show the modal
        $('#addHolidayModal').modal('show');

        // Check if title exists to disable the button accordingly
        $('#holiday-title').trigger('input'); // Trigger the input event to check for duplicates
    });
}

// AJAX check for duplicate holiday title

// AJAX check for duplicate holiday title
$('#holiday-title').on('input', function() {
    let title = $(this).val();
    $('#title-warning').hide(); // Hide the warning initially
    $('#submitBtn').attr('disabled', false); // Reset button state

    if (title.length > 0) {
        // Check if we are in add mode or edit mode
        const isEditMode = $('#holiday-id').val() !== '';
        $.ajax({
            url: '{{ route("holidays.check-title") }}',
            method: 'GET',
            data: { title: title, editMode: isEditMode, id: $('#holiday-id').val() },
            success: function(response) {
                if (response.exists && !isEditMode) {
                    // Show warning if title already exists
                    $('#success-alert').hide(); // Hide success alert if visible
                    $('#title-warning').text('Holiday title already exists!').show(); // Update text and show warning
                    $('#submitBtn').attr('disabled', true); // Disable button if title exists
                } else {
                    $('#title-warning').hide(); // Hide warning if unique
                    $('#submitBtn').attr('disabled', false); // Enable button if unique
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    } else {
        $('#title-warning').hide(); // Hide if input is empty
        $('#submitBtn').attr('disabled', false); // Enable button if input is empty
    }
});




// Reset modal for adding new holiday
$('#addHolidayModal').on('hidden.bs.modal', function () {
    // Reset the modal form
    $('#holidayForm').attr('action', '{{ route('holidays.store') }}');
    $('#addHolidayModalLabel').text('Add Holiday');
    $('#submitBtn').text('Submit');
    $('#holidayForm')[0].reset();
    
    // Ensure modal is fully reset when closing from edit mode
    $('#holiday-id').val('');
    $('#title-warning').hide(); // Hide the warning on reset
    $('#submitBtn').attr('disabled', false); // Enable submit button
});

// Close the modal when the 'X' button is clicked
$('.close').on('click', function () {
    $('#addHolidayModal').modal('hide'); // Close the modal manually
});
</script>



<style>
    /* Center the modal */
    .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
        margin: 1rem auto; /* Center modal */
    }
</style>
@endsection
