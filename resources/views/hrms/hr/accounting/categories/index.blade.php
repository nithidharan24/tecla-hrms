@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Categories');
@endphp
@extends('layouts.index')

@section('content')                
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Categories Management</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Categories</li>
                </ul>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" id="error-message">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary" id="showCategoriesBtn">Categories</button>
                <button type="button" class="btn btn-secondary" id="showSubcategoriesBtn">Subcategories</button>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="row" id="categoriesSection">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title float-start">Categories</h4>
                    @if(isset($permissions) && $permissions->can_create)
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#add_category">
                        <i class="fa fa-plus"></i> Add Category
                    </button>
                    @endif
                </div>
                <div class="card-body">
                <div class="table-responsive category-table-container">
    @if($categories->count())
        <table id="category-table" class="table custom-table datatable">
            <thead>
                <tr>
                    <th style="width:36px;">
                        <input type="checkbox" class="od-check" id="checkAllCategory" aria-label="Select all">
                    </th>
                    <th>S.No</th>
                    <th>Category Name</th>
                   @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr id="category-row-{{ $category->id }}">
                    <td>
                        <input type="checkbox" class="od-check row-check-category" aria-label="Select row">
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="od-chip-highlight">{{ $category->name }}</span></td>
                    <td class="text-end">
                        <div class="od-inline-actions">
                            @if(isset($permissions) && $permissions->can_edit)
                            <a class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#edit_category_{{ $category->id }}" title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            @endif
                            @if(isset($permissions) && $permissions->can_delete)
                            <a class="od-icon-btn danger" data-bs-toggle="modal" data-bs-target="#delete_category_{{ $category->id }}" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-light text-center">No categories found</div>
    @endif
</div>

                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories Section (Hidden by Default) -->
    <div class="row" id="subcategoriesSection" style="display: none;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title float-start">Subcategories</h4>
                    @if(isset($permissions) && $permissions->can_create)
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#add_subcategory">
                        <i class="fa fa-plus"></i> Add Subcategory
                    </button>
                    @endif
                </div>
                <div class="card-body">
                <div class="table-responsive subcategory-table-container">
    @if($subcategories->count())
        <table id="subcategory-table" class="table custom-table datatable">
            <thead>
                <tr>
                    <th style="width:36px;">
                        <input type="checkbox" class="od-check" id="checkAllSubcategory" aria-label="Select all">
                    </th>
                    <th>S.No</th>
                    <th>Subcategory Name</th>
                    <th>Parent Category</th>
                    <th class="text-end no-sort">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subcategories as $subcategory)
                <tr id="subcategory-row-{{ $subcategory->id }}">
                    <td>
                        <input type="checkbox" class="od-check row-check-subcategory" aria-label="Select row">
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="od-chip-highlight">{{ $subcategory->name }}</span></td>
                    <td><span class="high">{{ $subcategory->category_name }}</span></td>
                    <td class="text-end">
                        <div class="od-inline-actions">
                            @if(isset($permissions) && $permissions->can_edit)
                            <a class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#edit_subcategory_{{ $subcategory->id }}" title="Edit">
                                <i class="fa-solid fa-pencil"></i>
                            </a>
                            @endif
                            @if(isset($permissions) && $permissions->can_delete)
                            <a class="od-icon-btn danger" data-bs-toggle="modal" data-bs-target="#delete_subcategory_{{ $subcategory->id }}" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-light text-center">No subcategories found</div>
    @endif
</div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="add_category" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Category</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('categories.store-category') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Subcategory Modal -->
<div id="add_subcategory" class="modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Subcategory</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('categories.store-subcategory') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Category</label>
                        <select class="form-control" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subcategory Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit/Delete Modals -->
@foreach($categories as $category)
    <!-- Category Edit Modal -->
    <div id="edit_category_{{ $category->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categories.update-category', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Category Delete Modal -->
    <div id="delete_category_{{ $category->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this category and all its subcategories?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('categories.destroy-category', $category->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach($subcategories as $subcategory)
    <!-- Subcategory Edit Modal -->
    <div id="edit_subcategory_{{ $subcategory->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subcategory</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categories.update-subcategory', $subcategory->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Category</label>
                            <select class="form-control" name="category_id" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $cat->id == $subcategory->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subcategory Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $subcategory->name }}" required>
                        </div>
                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Subcategory Delete Modal -->
    <div id="delete_subcategory_{{ $subcategory->id }}" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Subcategory</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this subcategory?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('categories.destroy-subcategory', $subcategory->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
$(document).ready(function() {
    // Hide messages after 3 seconds
    setTimeout(function() {
        $('#success-message, #error-message').fadeOut('slow');
    }, 3000);

    // Toggle between Categories and Subcategories
    $('#showCategoriesBtn').click(function() {
        $('#categoriesSection').show();
        $('#subcategoriesSection').hide();
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        $('#showSubcategoriesBtn').removeClass('btn-primary').addClass('btn-secondary');
    });

    $('#showSubcategoriesBtn').click(function() {
        $('#categoriesSection').hide();
        $('#subcategoriesSection').show();
        $(this).removeClass('btn-secondary').addClass('btn-primary');
        $('#showCategoriesBtn').removeClass('btn-primary').addClass('btn-secondary');
    });

    // Initialize with Categories shown
    $('#showCategoriesBtn').trigger('click');
});






// Category table
const checkAllCategory = document.getElementById('checkAllCategory');
const rowsCategory = document.querySelectorAll('.row-check-category');

checkAllCategory?.addEventListener('change', function() {
    rowsCategory.forEach(r => {
        r.checked = this.checked;
        r.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
rowsCategory.forEach(r => {
    r.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

// Subcategory table
const checkAllSubcategory = document.getElementById('checkAllSubcategory');
const rowsSubcategory = document.querySelectorAll('.row-check-subcategory');

checkAllSubcategory?.addEventListener('change', function() {
    rowsSubcategory.forEach(r => {
        r.checked = this.checked;
        r.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
rowsSubcategory.forEach(r => {
    r.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

</script>

@endsection