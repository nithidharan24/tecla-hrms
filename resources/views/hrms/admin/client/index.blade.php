@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Clients');
@endphp

<!-- ============================= -->
<!-- TOP BAR -->
<!-- ============================= -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <h4 class="fw-bold mb-0">Clients</h4>

    <div class="d-flex align-items-center gap-2">

        @if(isset($permissions) && $permissions->can_create)
        <a href="{{route('client.create')}}" class="btn btn-primary px-3">
            <i class="fa fa-plus me-1"></i> Add Client
        </a>
        @endif

        <button id="openFilterBtn" class="filter-square-btn">
            <i class="fa-solid fa-filter"></i>
        </button>
        

        

    </div>
</div>
<!-- ============================= -->
<!-- SLIDE-IN FILTER PANEL -->
<div id="filterPanel" class="filter-slide-panel">
    <form method="GET" action="{{ route('client.index') }}">
        <div class="filter-header d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0">Filter Clients</h5>
            <button id="closeFilterBtn" type="button" class="btn btn-sm btn-light">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="p-3">

            <!-- Client ID -->
            <div class="mb-3">
                <label class="form-label fw-bold">Client ID</label>
                <input type="text" name="client_id" class="form-control"
                       value="{{ request('client_id') }}">
            </div>

            <!-- Client Name -->
            <div class="mb-3">
                <label class="form-label fw-bold">Client Name</label>
                <input type="text" name="client_name" class="form-control"
                       value="{{ request('client_name') }}">
            </div>

            <!-- Company -->
            <div class="mb-3">
                <label class="form-label fw-bold">Company</label>
                <select name="company" class="form-select">
                    <option value="">All Companies</option>
                    @foreach ($company as $id => $name)
                        <option value="{{ $name }}" 
                            {{ request('company') == $name ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- STATUS -->
            <div class="mb-3">
                <label class="form-label fw-bold">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="live" {{ request('status')=='live'?'selected':'' }}>Live</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                    <option value="wip" {{ request('status')=='wip'?'selected':'' }}>WIP</option>
                    <option value="staging" {{ request('status')=='staging'?'selected':'' }}>Staging</option>
                    <option value="client_not_renewed" {{ request('status')=='client_not_renewed'?'selected':'' }}>
                        Not Renewed
                    </option>
                </select>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button class="btn btn-primary w-100" type="submit">Apply Filters</button>

                <a href="{{ route('client.index') }}" class="btn btn-light border w-100">
                    Reset
                </a>
            </div>

        </div>
    </form>
</div>

<!-- ============================= -->
<!-- IMPORT MODAL -->
<!-- ============================= -->
<div class="modal fade" id="importClientModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Import Clients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <a href="{{ route('client.downloadTemplate') }}" class="btn btn-success mb-3 w-100">
                    <i class="fa fa-download"></i> Download Template
                </a>

                <form action="{{ route('client.importClients') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" class="form-control mb-3" required>
                    <button type="submit" class="btn btn-primary w-100">Import</button>
                </form>

            </div>

        </div>
    </div>
</div>

<!-- /Page Header -->
    
@if(isset($permissions) && $permissions->can_delete)
<button type="button" class="btn btn-danger" id="bulkDeleteBtn">
    <i class="fa-solid fa-trash"></i> Delete Selected
</button>
@endif

<div class="row">
    <div class="employee-table-container">
        <div class="table-responsive">
            <form action="{{ route('client.bulkDelete') }}" method="POST" id="bulkDeleteForm">
                @csrf
                <table id="client-table" class="table custom-table datatable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Company</th>
                            <th>Client ID</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Services</th>
                            <th>AMC Reminder Days</th>
                            <th>Status</th>
                            @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
                                <th class="text-end">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clients as $cs)
                            <tr id="client-row-{{ $cs->client_id }}">
                                <td>
                                    <input type="checkbox" name="ids[]" value="{{ $cs->client_id }}" class="rowCheckbox">
                                </td>
                                <td data-label="Company Name"><strong>{{ ucfirst($cs->company_name) }}</strong></td>
                    
                                <td data-label="Client ID"><span class="od-chip-highlight">{{ $cs->client_id }}</span></td>
                    
                                <td data-label="Contact Person">{{ ucfirst($cs->first_name) }}</td>
                    
                                <td data-label="Email">{{ $cs->email }}</td>
                    
                                <td data-label="Mobile">{{ $cs->phone }}</td>
                    
                                <td data-label="Services">
                                    @foreach(json_decode($cs->services ?? '[]', true) as $serviceId)
                                        @php $service = DB::table('services')->find($serviceId); @endphp
                                        @if($service)
                                            <span class="badge bg-success-light text-success me-1">{{ $service->name }}</span>
                                        @endif
                                    @endforeach
                                </td>

                                <!-- AMC Reminder Days Column with Inline Edit -->
                                <td data-label="AMC Reminder Days">
                                    <div class="amc-reminder-days-container" id="amc-container-{{ $cs->client_id }}">
                                        @if($permissions->can_edit)
                                            <div class="d-flex align-items-center">
                                                <span class="amc-display me-2 {{ $cs->amc_reminder_days ? 'badge bg-primary' : 'text-muted' }}">
                                                    {{ $cs->amc_reminder_days ? $cs->amc_reminder_days . ' days' : 'Not set' }}
                                                </span>
                                               
                                            </div>
                                        @else
                                            <span class="badge {{ $cs->amc_reminder_days ? 'bg-primary' : 'bg-secondary' }}">
                                                {{ $cs->amc_reminder_days ? $cs->amc_reminder_days . ' days' : 'Not set' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                    
                                <td data-label="Status">
                                    <div class="dropdown action-label">
                                        <button class="btn btn-white btn-sm btn-rounded dropdown-toggle"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fa-regular fa-circle-dot 
                                                {{ $cs->status == 'completed' ? 'text-success' : 
                                                   ($cs->status == 'live' ? 'text-info' : 
                                                   ($cs->status == 'wip' ? 'text-warning' :
                                                   ($cs->status == 'staging' ? 'text-primary' :
                                                   ($cs->status == 'client_not_renewed' ? 'text-danger' : 'text-secondary')))) }}">
                                            </i>
                                            {{ ucfirst(str_replace('_', ' ', $cs->status)) }}
                                        </button>
                    
                                        <div class="dropdown-menu dropdown-menu-end shadow-sm">
                                            @php
                                                $statuses = [
                                                    'completed' => ['text-success', 'Completed'],
                                                    'live' => ['text-info', 'Live'],
                                                    'wip' => ['text-warning', 'WIP'],
                                                    'staging' => ['text-primary', 'Staging'],
                                                    'staging_waiting_approval' => ['text-info', 'Staging waiting approval'],
                                                    'staging_client_modified' => ['text-warning', 'Client modified work'],
                                                    'client_not_renewed' => ['text-danger', 'Client not renewed'],
                                                    'file_moved_to_client' => ['text-secondary', 'File moved to client']
                                                ];
                                            @endphp
                                            @foreach($statuses as $key => [$color, $label])
                                                <button type="button" 
                                                        class="dropdown-item status-change-btn {{ $cs->status == $key ? 'disabled' : '' }}"
                                                        data-client-id="{{ $cs->client_id }}"
                                                        data-status="{{ $key }}">
                                                    <i class="fa-regular fa-circle-dot {{ $color }}"></i> {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                    
                                <td data-label="Actions" class="text-end">
                                    <div class="od-inline-actions">
                                        @if($permissions->can_edit)
                                            <a href="{{ route('client.edit', $cs->client_id) }}" 
                                               class="od-icon-btn" title="Edit">
                                                <i class="fa-solid fa-pencil"></i>
                                            </a>
                                        @endif
                                        @if($permissions->can_delete)
                                            <button type="button" class="od-icon-btn danger" 
                                                    onclick="deleteClient('{{ $cs->client_id }}')" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fa-regular fa-folder-open me-1"></i>No Clients Found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<!-- AMC Reminder Days Edit Modal -->
<div class="modal fade" id="amcReminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set AMC Reminder Days</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="amcReminderForm">
                    @csrf
                    <input type="hidden" id="editClientId" name="client_id">
                    <div class="input-block mb-3">
                        <label class="col-form-label">Reminder Days <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amcReminderDays" 
                               name="amc_reminder_days" min="1" max="365" required
                               placeholder="Enter days before AMC expiry">
                        <small class="form-text text-muted">Reminder will be sent X days before AMC expiry</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
               
                <button type="button" class="btn btn-primary" id="saveAmcReminder">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Right slide filter */
    .filter-slide-panel {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: #fff;
        z-index: 9999;
        transition: all 0.3s ease;
        box-shadow: -4px 0 15px rgba(0,0,0,0.1);
    }

    .filter-slide-panel.active {
        right: 0;
    }
    
    /* Square Filter Icon (Zoho Style) */
    .filter-square-btn {
        width: 42px;
        height: 42px;
        background: #fff;
        border: 1px solid #d6d6d6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #595959;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }

    .filter-square-btn:hover {
        background: #f5f5f5;
        border-color: #c9c9c9;
    }

    .filter-square-btn i {
        font-size: 16px;
    }

    @media (max-width: 576px) {
        .filter-slide-panel {
            width: 100%;
        }

        .btn {
            font-size: 14px;
        }

        table {
            font-size: 12px;
        }
    }
</style>

<script>
$(document).ready(function() {
    // Status change using event delegation
    $(document).on('click', '.status-change-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if ($(this).hasClass('disabled')) return;
        
        const clientId = $(this).data('client-id');
        const status = $(this).data('status');
        
        changeClientStatus(clientId, status);
    });
    
    function changeClientStatus(clientId, status) {
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
                    type: "POST",
                    url: "{{ route('client.changeStatus') }}",
                    data: { 
                        id: clientId, 
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if (data.status === 1 || data.status === 2 || data.status === 'success') {
                            Swal.fire({
                                title: "Success",
                                text: data.message || "Client status changed successfully.",
                                icon: "success",
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Update the status display without reloading
                                updateStatusDisplay(clientId, status);
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: data.message || "Failed to update status",
                                icon: "error"
                            });
                        }
                    },
                    error: function (error) {
                        console.error("Error updating client status:", error);
                        Swal.fire({
                            title: "Error",
                            text: "Failed to update client status.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }
    
    function updateStatusDisplay(clientId, newStatus) {
        const row = $(`#client-row-${clientId}`);
        if (row.length === 0) return;
        
        const statusBtn = row.find('.dropdown-toggle');
        const dropdownItems = row.find('.status-change-btn');
        
        // Update the status button text and icon
        const statusText = newStatus.replace(/_/g, ' ')
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
        
        const colorClass = getStatusColor(newStatus);
        statusBtn.html(`<i class="fa-regular fa-circle-dot ${colorClass}"></i> ${statusText}`);
        
        // Update dropdown items active state
        dropdownItems.each(function() {
            const itemStatus = $(this).data('status');
            if (itemStatus === newStatus) {
                $(this).addClass('disabled');
            } else {
                $(this).removeClass('disabled');
            }
        });
    }
    
    function getStatusColor(status) {
        switch(status) {
            case 'completed': return 'text-success';
            case 'live': return 'text-info';
            case 'wip': return 'text-warning';
            case 'staging': return 'text-primary';
            case 'staging_waiting_approval': return 'text-info';
            case 'staging_client_modified': return 'text-warning';
            case 'client_not_renewed': return 'text-danger';
            case 'file_moved_to_client': return 'text-secondary';
            default: return 'text-secondary';
        }
    }
    
    // AMC Reminder Days functionality
    const amcModal = new bootstrap.Modal(document.getElementById('amcReminderModal'));
    
    // Edit button click handler
    $(document).on('click', '.edit-amc-btn', function() {
        const clientId = $(this).data('client-id');
        const currentDays = $(this).data('current-days');
        
        $('#editClientId').val(clientId);
        $('#amcReminderDays').val(currentDays || '');
        
        amcModal.show();
    });
    
    // Save AMC Reminder Days
    $('#saveAmcReminder').on('click', function() {
        const clientId = $('#editClientId').val();
        const reminderDays = $('#amcReminderDays').val();
        
        if (!reminderDays || reminderDays < 1 || reminderDays > 365) {
            Swal.fire({
                title: "Error",
                text: "Please enter a valid number between 1 and 365",
                icon: "error"
            });
            return;
        }
        
        // Show loading
        const saveBtn = $(this);
        const originalText = saveBtn.html();
        saveBtn.html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');
        saveBtn.prop('disabled', true);
        
        $.ajax({
            type: "POST",
            url: "{{ route('client.updateAmcReminder') }}",
            data: {
                client_id: clientId,
                amc_reminder_days: reminderDays,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update the display
                    const container = $(`#amc-container-${clientId}`);
                    const displaySpan = container.find('.amc-display');
                    const editBtn = container.find('.edit-amc-btn');
                    
                    displaySpan.text(reminderDays + ' days');
                    displaySpan.removeClass('text-muted').addClass('badge bg-primary');
                    editBtn.data('current-days', reminderDays);
                    
                    amcModal.hide();
                    
                    Swal.fire({
                        title: "Success",
                        text: "AMC reminder days updated successfully!",
                        icon: "success",
                        timer: 2000
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.message,
                        icon: "error"
                    });
                }
            },
            error: function(xhr) {
                console.error("Error updating AMC reminder days:", xhr);
                Swal.fire({
                    title: "Error",
                    text: "Failed to update AMC reminder days",
                    icon: "error"
                });
            },
            complete: function() {
                saveBtn.html(originalText);
                saveBtn.prop('disabled', false);
            }
        });
    });
    
    // Clear form when modal is hidden
    $('#amcReminderModal').on('hidden.bs.modal', function() {
        $('#amcReminderForm')[0].reset();
    });
    
    // OPEN FILTER PANEL
    $('#openFilterBtn').on('click', function() {
        $('#filterPanel').addClass('active');
    });
    
    // CLOSE FILTER PANEL
    $('#closeFilterBtn').on('click', function() {
        $('#filterPanel').removeClass('active');
    });
    
    // Reset Filter
    $('#resetFilterBtn').on('click', function() {
        $('#clientId').val('');
        $('#clientName').val('');
        $('#companyName').val('');
        $('#statusFilter').val('');
    });
    
    // IMPORT POPUP
    $('#moreOptionsBtn').on('click', function() {
        new bootstrap.Modal($('#importClientModal')).show();
    });
    
    // Select all checkboxes
    $('#selectAll').on('click', function() {
        $('.rowCheckbox').prop('checked', this.checked);
    });
    
    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const anySelected = $('.rowCheckbox:checked').length > 0;
        
        if (!anySelected) {
            Swal.fire("No clients selected", "Please select at least one client", "warning");
            return;
        }
        
        Swal.fire({
            title: "Are you sure?",
            text: "Selected clients will be deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#bulkDeleteForm').submit();
            }
        });
    });
});

// Delete single client
function deleteClient(id) {
    Swal.fire({
        title: "Are you sure you want to delete this Client?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        allowOutsideClick: false,
    }).then(function(result) {
        if (result.isConfirmed) {
            var url = "{{ route('client.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    Swal.fire({
                        title: "Deleted!",
                        text: "Client has been deleted.",
                        icon: "success"
                    }).then(() => {
                        $('#client-row-' + id).remove();
                    });
                },
                error: function (error) {
                    console.error("Error deleting client:", error);
                    Swal.fire({
                        title: "Error",
                        text: "Failed to delete client.",
                        icon: "error"
                    });
                }
            });
        }
    });
}
</script>

@if (session('message'))
    <script>
        Swal.fire({
            icon: '{{ session("messageType") }}',
            title: '{{ session("messageType") }}',
            text: '{{ session("message") }}',
            timer: 2500
        });
    </script>
@endif