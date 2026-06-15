@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Shortlist Candidates');
@endphp
@extends('layouts.index')

@section('content')

<!-- Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Candidates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Candidates</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('candidate.create') }}" class="btn add-btn">
                     Add Candidate
                </a>
                @endif
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped datatable mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Created Date</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $candidate) <!-- Make sure to pass $candidates from your controller -->
                        <tr>
                            <td>{{ $candidate->id }}</td>
                            <td>{{ $candidate->first_name . ' ' . $candidate->last_name }}</td> <!-- Combined Name -->
                            <td>{{ $candidate->email }}</td> <!-- Email -->
                            <td>{{ $candidate->phone }}</td> <!-- Phone Number -->
                            <td>{{ \Carbon\Carbon::parse($candidate->created_at)->format('d-m-Y') }}</td> <!-- Created Date -->
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if(isset($permissions) && $permissions->can_edit)
                                            <a class="dropdown-item" href="{{ route('candidate.edit', $candidate->id) }}">
                                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                                            </a>             
                                            @endif
                                            @if(isset($permissions) && $permissions->can_delete)                               
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_candidate_{{ $candidate->id }}">
                                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="delete_candidate_{{ $candidate->id }}" tabindex="-1" role="dialog" aria-labelledby="delete_candidate_label_{{ $candidate->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="delete_candidate_label_{{ $candidate->id }}">
                                                    <i class="fas fa-exclamation-triangle"></i> Delete Candidate
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center py-5">
                                                <p class="fs-5 text-muted">
                                                    Are you sure you want to delete this candidate? This action cannot be undone.
                                                </p>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <i class="fas fa-trash-alt fa-3x text-danger"></i>
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                    <i class="fas fa-times-circle"></i> Cancel
                                                </button>
                                                <!-- DELETE FORM -->
                                                <form action="{{ route('candidate.destroy', $candidate->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE') <!-- This maps to the DELETE request -->
                                                    <button type="submit" class="btn btn-danger px-4">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Automatically hide success and error messages after 1.5 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
        let errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 1500);
</script>

@endsection
