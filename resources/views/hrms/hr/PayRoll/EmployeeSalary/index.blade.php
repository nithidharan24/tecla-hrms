@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Employee Salary Salaryartments');
@endphp
@extends('layouts.index')

@section('content')	

				<!-- Page Content -->
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row align-items-center">
							<div class="col">
								<h3 class="page-title">Employee Salary</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
									<li class="breadcrumb-item active">Salary</li>
								</ul>
							</div>
                            @if(isset($permissions) && $permissions->can_create)
							<div class="col-auto float-end ms-auto">
								<a href="{{ route('salary.create') }}" class="btn add-btn" > Add Salary</a>
							</div>
                            @endif
						</div>
					</div>
					<!-- /Page Header -->
					
					
					<div class="row">
						<div class="col-md-12">
						<div class="table-responsive">
    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="alert alert-success" id="success-message">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" id="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="table  custom-table mb-0 datatable">
        <thead>
            <tr>
               
                <th>Employee</th>
                <th>Employee ID</th>
                <th>Email</th>
                <th>Join Date</th>
                <th>Role</th>
                <th>Salary</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @foreach($salaries as $salary)
            <tr class="row-check" id="salary-row-{{ $salary->id }}">
                <td data-label="Employee">
                    <h2 class="table-avatar">
                        <a href="">
                            {{ $salary->firstname }} {{ $salary->lastname }} 
                            <span class="od-chip-highlight">{{ $salary->designation_name }}</span>
                        </a>
                    </h2>
                </td>
                
                <td class="high" data-label="Employee ID">{{ $salary->employeeid }}</td>
                
                <td class="text-muted" data-label="Email">
                    <span class="high">{{ $salary->email }}</span>
                </td>
                
                <td data-label="Joining Date">
                    {{ \Carbon\Carbon::parse($salary->joiningdate)->format('d-m-y') }}
                </td>
                
                <td data-label="Designation">{{ $salary->designation_name }}</td>
                
                <td data-label="Net Salary">{{ $salary->net_salary }}</td>
                
                <td class="text-end od-inline-actions" data-label="Actions">
                    @if(isset($permissions) && $permissions->can_edit)
                    <a href="{{ route('salary.edit', $salary->id) }}" class="od-icon-btn" title="Edit">
                        <i class="fa-solid fa-pencil"></i>
                    </a>
                    @endif
                
                    @if(isset($permissions) && $permissions->can_delete)
                    <a href="#" class="od-icon-btn" data-bs-toggle="modal" data-bs-target="#deleteSalaryModal_{{ $salary->id }}" title="Delete">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                    @endif
                
                    <form method="POST" action="{{ route('salary.send-hike-letter', $salary->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="od-icon-btn" title="Send Hike Letter">
                            <i class="fa-solid fa-envelope"></i>
                        </button>
                    </form>
                </td>
                
            </tr>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteSalaryModal_{{ $salary->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteSalaryLabel_{{ $salary->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteSalaryLabel_{{ $salary->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this salary record for {{ $salary->firstname }} {{ $salary->lastname }}?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form method="POST" action="{{ route('salary.destroy', $salary->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>



						</div>
					</div>
					<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSalaryModal" tabindex="-1" aria-labelledby="deleteSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h5 class="modal-title w-100 text-center" id="deleteSalaryModalLabel" style="font-weight: bold;">Delete Salary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                Are you sure you want to delete this salary record?
            </div>
            <div class="modal-footer d-flex justify-content-around border-0">
                <button type="button" class="btn btn-outline-warning btn-lg" data-bs-dismiss="modal" style="border-radius: 50px; width: 150px;">Cancel</button>

                <form id="deleteSalaryForm" method="POST" action="" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg" style="border-radius: 50px; width: 150px;">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

                </div>
				<!-- /Page Content -->
				<script>
    function setDeleteFormAction(id) {
        var form = document.getElementById('deleteSalaryForm');
       form.action = "{{ url('salary') }}/" + id;

    }
</script>

<!-- Optional JS for Row Highlight -->
<script>
    const checkAll = document.getElementById('checkAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');

    checkAll.addEventListener('change', function() {
        rowCheckboxes.forEach(cb => {
            cb.checked = this.checked;
            cb.closest('tr').classList.toggle('od-selected', this.checked);
        });
    });

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('tr').classList.toggle('od-selected', this.checked);
        });
    });
</script>

                @endsection