@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Experience Level');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Experience</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Jobs</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                    Add Experience
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Success/Error messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table custom-table datatable" id="employee-table">
                    <thead>
                        <tr>
                            <th>S.I.</th>
                            <th>Experience</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($experiences as $experience)
                        <tr>
                            <td data-label="S.I.">
                                {{ $loop->iteration }}
                            </td>
                            <td data-label="Experience">
                                <span class="od-chip-highlight">{{ $experience->experience }}</span>
                            </td>
                            <td data-label="Status">
                                <div class="od-status-toggle">
                                    <input type="checkbox" class="form-check-input status-toggle"
                                           id="statusToggle{{ $experience->id }}"
                                           data-id="{{ $experience->id }}"
                                           {{ $experience->status == 'active' ? 'checked' : '' }}>
                                    <label for="statusToggle{{ $experience->id }}" class="status-label">
                                        {{ $experience->status == 'active' ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </td>
                            <td data-label="Actions" class="text-end">
                                <div class="od-inline-actions">
                                    @if(isset($permissions) && $permissions->can_edit)
                                    <button class="od-icon-btn edit-btn" title="Edit" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editExperienceModal"
                                            data-id="{{ $experience->id }}"
                                            data-experience="{{ $experience->experience }}">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    @endif
                        
                                    @if(isset($permissions) && $permissions->can_delete)
                                    <form action="{{ route('experience.destroy', $experience->id) }}" 
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="od-icon-btn danger delete-btn" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fa fa-briefcase fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Experience Levels Found</h5>
                                    <p class="text-muted">Start by creating your first experience level.</p>
                                    @if(isset($permissions) && $permissions->can_create)
                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                                        <i class="fa fa-plus me-2"></i>Create Experience
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Experience Modal -->
<div class="modal fade" id="addExperienceModal" tabindex="-1" role="dialog" aria-labelledby="addExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="addExperienceModalLabel">Add Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addExperienceForm" action="{{ route('experience.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="experience" class="form-label">Experience Level (in years)</label>
                        <input type="number" 
                               class="form-control" 
                               id="experience" 
                               name="experience" 
                               required 
                               step="0.1" 
                               min="0.1" 
                               max="99.9"
                               placeholder="e.g., 2.5"
                               autocomplete="off">
                        <div class="form-text">Enter years of experience (e.g., 1, 2.5, 5.5)</div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary px-4 ms-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Experience Modal -->
<div class="modal fade" id="editExperienceModal" tabindex="-1" role="dialog" aria-labelledby="editExperienceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title" id="editExperienceModalLabel">Edit Experience</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editExperienceForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editExperienceId">
                    <div class="mb-3">
                        <label for="edit_experience" class="form-label">Experience Level</label>
                        <input type="text" class="form-control" id="edit_experience" name="experience" required autocomplete="off">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary px-4 ms-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- jQuery CDN --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Handle edit button click to populate modal
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var experience = $(this).data('experience');

            // Set the form action to the correct URL
            var formAction = "{{ route('experience.update', '') }}/" + id;
            $('#editExperienceForm').attr('action', formAction);

            // Populate the fields in the modal
            $('#editExperienceId').val(id);
            $('#edit_experience').val(experience);
        });

        // Disable submit button on form submission for both forms
        $('#addExperienceForm, #editExperienceForm').on('submit', function(e) {
            var $form = $(this);
            var $submitButton = $form.find('button[type="submit"]');

            // Disable button and change text
            $submitButton.prop('disabled', true).text('Submitting...');

            // Prevent double submission by disabling form resubmit
            if ($form.data('submitted')) {
                e.preventDefault();
            } else {
                $form.data('submitted', true);
            }
        });

        // SweetAlert for delete confirmation
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault(); // Prevent default form submission

            var form = $(this).closest('form'); // Get the parent form

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Submit the form if confirmed
                }
            });
        });

        // AJAX for Status Toggle
        $(document).on('change', '.status-toggle', function() {
            var id = $(this).data('id');
            var isChecked = $(this).is(':checked');
            var newStatus = isChecked ? 'active' : 'inactive';
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var $toggle = $(this);

            $.ajax({
                url: "{{ url('/experience') }}/" + id + "/toggle-status",
                type: 'POST',
                data: {
                    _token: csrfToken,
                    status: newStatus
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Update label text
                    $toggle.next('.status-label').text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                },
                error: function(xhr) {
                    Swal.fire('Error!', 'Failed to update status.', 'error');
                    // Revert toggle state on error
                    $toggle.prop('checked', !isChecked);
                }
            });
        });
    });
</script>
@endsection