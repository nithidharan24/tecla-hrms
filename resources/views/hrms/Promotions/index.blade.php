@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Promotion');
@endphp
@extends('layouts.index')

@section('content')

<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Promotions</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item active">Promotions</li>
            </ul>
        </div>
        {{-- <div class="col-auto float-end ms-auto">
            @if(isset($permissions) && $permissions->can_create)
            <a href="{{ route('promotion.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Promotion
            </a>
            @endif
        </div> --}}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
    <table id="promotion-table" class="table custom-table datatable">
        <thead>
            <tr>
               
                <th>ID</th>
                <th>Employee</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>From</th>
                <th>To</th>
                <th>Promotion Date</th>
                <th>Created At</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach($promotions as $promotion)
            <tr>
                <td data-label="ID" class="high">{{ $promotion->id }}</td>
                <td data-label="Employee Name"><span class="od-chip-highlight">{{ $promotion->firstname }} {{ $promotion->lastname }}</span></td>
                <td data-label="Employee ID">{{ $promotion->employeeid }}</td>
                <td data-label="Department">{{ $promotion->department_name }}</td>
                <td data-label="Promotion From">{{ $promotion->promotion_from }}</td>
                <td data-label="Promotion To">{{ $promotion->promotion_to }}</td>
                <td data-label="Promotion Date">{{ date('d M Y', strtotime($promotion->promotion_date)) }}</td>
                <td data-label="Created At">{{ date('d M Y H:i', strtotime($promotion->created_at)) }}</td>
                <td data-label="Actions" class="text-end">
                    <div class="od-inline-actions">
                        @if(isset($permissions) && $permissions->can_edit)
                        <a href="{{ route('promotion.edit', $promotion->id) }}" class="od-icon-btn" title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>
                        @endif
                        @if(isset($permissions) && $permissions->can_delete)
                        <form method="POST" action="{{ route('promotion.destroy', $promotion->id) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="od-icon-btn danger" title="Delete">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Odoo-style checkbox row selection -->
<script>
$(document).ready(function(){
    // Select/Deselect all rows
    $('#checkAll').on('change', function() {
        var checked = $(this).is(':checked');
        $('.row-check').prop('checked', checked).trigger('change');
    });

    // Row highlight on checkbox toggle
    $('.row-check').on('change', function() {
        if($(this).is(':checked')){
            $(this).closest('tr').addClass('od-selected');
        } else {
            $(this).closest('tr').removeClass('od-selected');
        }
    });

    // Click row to toggle checkbox
    $('#promotion-table tbody tr').on('click', function(e){
        if(!$(e.target).is('input[type="checkbox"], button, a')){
            var checkbox = $(this).find('.row-check');
            checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
        }
    });
});
</script>

    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        // Handle delete button click
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            if(confirm('Are you sure you want to delete this promotion?')) {
                $(this).closest('form').submit();
            }
        });
    });
</script>
@endsection