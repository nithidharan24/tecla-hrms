@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
  <div class="container-fluid dashboard-content">

      <!-- PAGE HEADER -->
      <div class="row mb-4">
          <div class="col-xl-12">
              <div class="card shadow-sm border-0">
                  <div class="card-body d-flex justify-content-between align-items-center">
                      <div>
                          <h3 class="fw-bold mb-1">Hierarchy Management</h3>
                          <p class="text-muted mb-0">
                              Manage hierarchy levels and control module access permissions.
                          </p>
                      </div>
                      <div>
                          <a href="{{ route('hierarchy.create') }}" class="btn btn-primary">
                              <i class="fas fa-plus me-2"></i> Add Hierarchy Level
                          </a>
                      </div>
                  </div>
              </div>
          </div>
      </div>

      <!-- FILTER BAR -->
      <div class="row mb-4">
          <div class="col-xl-12">
              <div class="card shadow-sm border-0">
                  <div class="card-body">
                      <div class="row align-items-end">

                          <div class="col-md-6">
                              <label class="form-label fw-semibold">Search Hierarchy</label>
                              <input type="text"
                                     id="searchHierarchy"
                                     class="form-control"
                                     placeholder="Type hierarchy name to search...">
                          </div>

                          <div class="col-md-6 text-md-end mt-3 mt-md-0">
                              <button class="btn btn-outline-secondary"
                                      onclick="refreshTable()">
                                  <i class="fas fa-sync-alt me-1"></i> Refresh
                              </button>
                             
                          </div>

                      </div>
                  </div>
              </div>
          </div>
      </div>
<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-message-success">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-message-error">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
    // Auto-dismiss both success and error alerts after 3 seconds
    setTimeout(function() {
        let successAlert = document.getElementById('alert-message-success');
        if (successAlert) {
            let bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }

        let errorAlert = document.getElementById('alert-message-error');
        if (errorAlert) {
            let bsAlert = new bootstrap.Alert(errorAlert);
            bsAlert.close();
        }
    }, 3000);
</script>



      
        <!-- Hierarchy Table -->
        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Hierarchy Levels List</h4>
                        
                    </div>
                    <div class="card-body">
                       <div class="table-responsive">
    <table id="hierarchyTable" class="table custom-table datatable">
        <thead>
            <tr>
                
                <th>S.No</th>
                <th>Hierarchy Level</th>
                <th>Module Access</th>
                @if(isset($permissions) && ($permissions->can_edit || $permissions->can_delete))
    <th class="text-end">Actions</th>
@endif
            </tr>
        </thead>
        <tbody>
            @forelse($hierarchies as $index => $hierarchy)
            <tr>
              <td data-label="S. No.">{{ $index + 1 }}</td>

              <td data-label="Hierarchy Level">
                  <span class="od-chip-highlight">
                      {{ $hierarchy->hierarchy_level }}
                  </span>
                  <br>
                  <small class="text-muted">Created: {{ \Carbon\Carbon::parse($hierarchy->created_at)->format('M d, Y') }}</small>
              </td>
              
              <td data-label="Modules">
                  @php
                      $modules = json_decode($hierarchy->modules ?? '[]', true);
                      $moduleCount = is_array($modules) ? count($modules) : 0;
                  @endphp
                  <span class="badge bg-info">{{ $moduleCount }} Modules</span>
                
              </td>
              
              <td data-label="Actions" class="text-end">
                  <div class="od-inline-actions">
                      <button class="od-icon-btn" title="View Details" onclick="viewHierarchy({{ $hierarchy->id }})">
                          <i class="fa-solid fa-eye"></i>
                      </button>
              
                      @if(isset($permissions) && $permissions->can_edit)
                      <a href="{{ route('hierarchy.edit', $hierarchy->id) }}" class="od-icon-btn" title="Edit">
                          <i class="fa-solid fa-pencil"></i>
                      </a>
                      @endif
              
                      @if(isset($permissions) && $permissions->can_delete)
                      <button class="od-icon-btn danger" title="Delete" onclick="deleteHierarchy({{ $hierarchy->id }})">
                          <i class="fa-solid fa-trash"></i>
                      </button>
                      @endif
                  </div>
              </td>
              
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Hierarchy Levels Found</h5>
                        <p class="text-muted">Start by creating your first hierarchy level.</p>
                        @if(isset($permissions) && $permissions->can_add)
                        <a href="{{ route('hierarchy.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Hierarchy Level
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Checkbox Script -->
<script>
const checkAllHierarchy = document.getElementById('checkAllHierarchy');
const rowChecksHierarchy = document.querySelectorAll('.row-check-hierarchy');

checkAllHierarchy?.addEventListener('change', function() {
    rowChecksHierarchy.forEach(cb => {
        cb.checked = this.checked;
        cb.closest('tr').classList.toggle('od-selected', this.checked);
    });
});

rowChecksHierarchy.forEach(cb => {
    cb.addEventListener('change', function() {
        this.closest('tr').classList.toggle('od-selected', this.checked);
    });
});
</script>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modules Modal -->
<div class="modal fade" id="modulesModal" tabindex="-1" aria-labelledby="modulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modulesModalLabel">Hierarchy Level Module Access</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modulesModalBody">
                <!-- Module content will be loaded here -->
            </div>
            
        </div>
    </div>
</div>

<!-- Hierarchy Details Modal -->
<div class="modal fade" id="hierarchyModal" tabindex="-1" aria-labelledby="hierarchyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hierarchyModalLabel">Hierarchy Level Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="hierarchyModalBody">
                <!-- Hierarchy details will be loaded here -->
            </div>
           
        </div>
    </div>
</div>

<!-- CSRF Token Meta Tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .table img {
        object-fit: cover;
    }
    
    .empty-state {
        padding: 40px 20px;
    }
    
    .card-header-actions {
        display: flex;
        gap: 10px;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    
    .table-responsive {
        border-radius: 8px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
  
      const searchHierarchy = document.getElementById("searchHierarchy");
  
      function filterTable() {
          const searchValue = searchHierarchy.value.toLowerCase();
          const rows = document.querySelectorAll("#hierarchyTable tbody tr");
  
          rows.forEach((row) => {
  
              // Skip empty state row
              if (row.cells.length === 1) return;
  
              const rowText = row.textContent.toLowerCase();
  
              if (rowText.includes(searchValue)) {
                  row.style.display = "";
              } else {
                  row.style.display = "none";
              }
          });
      }
  
      searchHierarchy.addEventListener("input", filterTable);
  
  });
  </script>
@endsection