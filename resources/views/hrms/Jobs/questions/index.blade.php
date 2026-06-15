@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Interview Questions');
@endphp
@extends('layouts.index')
@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Questions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Questions</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                @if(isset($permissions) && $permissions->can_create)
                <a href="{{ route('Question.create') }}" class="btn add-btn">
                    <i class="fa fa-plus"></i> Add Question
                </a>
                @endif
                {{-- New Download PDF Button --}}
                <a href="{{ route('Question.downloadPdf') }}" class="btn btn-primary me-2">
                    <i class="fa fa-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-message">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="error-message">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Filters</h5>
            <form id="filter-form" method="GET" action="{{ route('Question.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Search questions, options, category...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category">
                                <option value="">All Categories</option>
                                @isset($categories)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category }}" {{ request('category') == $category->category ? 'selected' : '' }}>
                                            {{ $category->category }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" class="form-control" name="start_date" 
                                           value="{{ request('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" class="form-control" name="end_date" 
                                           value="{{ request('end_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="padding-top: 27px;">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-filter"></i> Apply
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" onclick="resetFilters()">
                                <i class="fa-solid fa-rotate-right"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Filters Card -->

    <div class="row">
        <div class="col-md-12">
           <div class="table-responsive">
    <table id="question-table" class="table table-striped custom-table datatable mb-0">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Question</th>
                <th>Category</th>
                <th>Created Date</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
            <tr>
                <td data-label="ID">{{ $question->id }}</td>
                <td data-label="Question">
                    <a href="{{ route('Question.show', $question->id) }}" class="text-primary">
                        {!! nl2br(e(Str::limit($question->question, 50))) !!}
                    </a>
                </td>
                <td data-label="Category">
                    <span class="badge bg-info-light">{{ $question->category }}</span>
                </td>
                <td data-label="Created At">{{ \Carbon\Carbon::parse($question->created_at)->format('d-m-Y') }}</td>
                <td data-label="Actions" class="text-end">
                    <div class="dropdown dropdown-action">
                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons">more_vert</i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('Question.show', $question->id) }}">
                                <i class="fa-solid fa-eye m-r-5"></i> View
                            </a>
                            @if(isset($permissions) && $permissions->can_edit)
                            <a class="dropdown-item" href="{{ route('Question.edit', $question->id) }}">
                                <i class="fa-solid fa-pencil m-r-5"></i> Edit
                            </a>
                            @endif
                            @if(isset($permissions) && $permissions->can_delete)
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_question_{{ $question->id }}">
                                <i class="fa-regular fa-trash-can m-r-5"></i> Delete
                            </a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Delete Confirmation Modal -->
            <div class="modal custom-modal fade" id="delete_question_{{ $question->id }}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="form-header">
                                <h3>Delete Question</h3>
                                <p>Are you sure you want to delete this question?</p>
                            </div>
                            <div class="modal-btn delete-action">
                                <div class="row">
                                    <div class="col-6">
                                        <form action="{{ route('Question.destroy', $question->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-primary continue-btn w-100">Delete</button>
                                        </form>
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
            <!-- /Delete Confirmation Modal -->
            @endforeach
        </tbody>
    </table>
</div>

            <!-- Pagination -->
            @if($questions->count() > 0)
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $questions->firstItem() }} to {{ $questions->lastItem() }} of {{ $questions->total() }} entries
                        </div>
                        <div>
                            {{ $questions->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Auto-submit form on filter change
        $('select[name="category"]').on('change', function() {
            $('#filter-form').submit();
        });

        // Enter key support for search
        $('input[name="search"]').on('keypress', function(e) {
            if (e.which === 13) {
                $('#filter-form').submit();
            }
        });
    });

    function resetFilters() {
        window.location.href = '{{ route("Question.index") }}';
    }

    // Automatically hide success and error messages after 3 seconds
    setTimeout(function() {
        let successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
        let errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
    }, 3000);
</script>

<style>
.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-title {
    color: #333;
    font-weight: 600;
    margin-bottom: 20px;
    font-size: 16px;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-control {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn {
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
}

.badge {
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 12px;
}

.bg-info-light {
    background-color: #d1ecf1;
    color: #0c5460;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.dropdown-menu {
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.dropdown-item {
    padding: 8px 16px;
    font-size: 14px;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}

.text-muted {
    color: #6c757d !important;
}

.pagination {
    margin-bottom: 0;
}
</style>

@endsection