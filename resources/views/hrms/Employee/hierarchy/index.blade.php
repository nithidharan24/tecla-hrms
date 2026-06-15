@php
    $permissions = App\Helpers\PermissionHelper::getPermissions('Settings');
@endphp
@extends('layouts.index')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
.hierarchy-page-header {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #fff;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.hierarchy-page-header::before {
    content: '';
    position: absolute;
    top: -40%; right: -5%;
    width: 260px; height: 260px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.hierarchy-page-header::after {
    content: '';
    position: absolute;
    bottom: -50%; right: 8%;
    width: 180px; height: 180px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.hierarchy-page-header h1 { font-size: 1.6rem; font-weight: 700; margin: 0 0 4px; }
.hierarchy-page-header p { margin: 0; opacity: .8; font-size: .9rem; }
.hierarchy-page-header .breadcrumb { margin: 0; padding: 0; background: transparent; }
.hierarchy-page-header .breadcrumb-item,
.hierarchy-page-header .breadcrumb-item a { color: rgba(255,255,255,.75); font-size: .82rem; text-decoration: none; }
.hierarchy-page-header .breadcrumb-item.active { color: #fff; }
.hierarchy-page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,.5); }
.btn-header-action {
    background: rgba(255,255,255,.18);
    border: 1.5px solid rgba(255,255,255,.4);
    color: #fff !important;
    border-radius: 10px;
    padding: 9px 22px;
    font-weight: 600;
    font-size: .88rem;
    transition: background .2s;
    position: relative;
    z-index: 5;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.btn-header-action:hover { background: rgba(255,255,255,.32); color: #fff !important; }
.filter-card { border: 0; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 20px; }
.filter-card .card-body { padding: 18px 22px; }
.filter-card .form-control {
    border-radius: 10px;
    border: 1.5px solid #e5e7eb;
    font-size: .88rem;
    padding: 9px 14px;
    transition: border-color .2s, box-shadow .2s;
}
.filter-card .form-control:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249,115,22,.1); outline: none; }
.btn-filter-refresh {
    border-radius: 10px; padding: 9px 20px; font-size: .85rem; font-weight: 600;
    border: 1.5px solid #e5e7eb; color: #374151; background: #fff; transition: all .2s;
}
.btn-filter-refresh:hover { background: #f3f4f6; border-color: #d1d5db; }
.hierarchy-table-card { border: 0; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow: hidden; }
.hierarchy-table-card .card-header {
    background: #fff; border-bottom: 1px solid #f3f4f6; padding: 18px 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.hierarchy-table-card .card-header h5 { font-size: 1rem; font-weight: 700; color: #111827; margin: 0; }
.table-modern thead th {
    background: #f9fafb; color: #6b7280; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid #f3f4f6; padding: 13px 18px; white-space: nowrap;
}
.table-modern tbody tr { transition: background .15s; }
.table-modern tbody tr:hover { background: #fafafa; }
.table-modern td { padding: 14px 18px; vertical-align: middle; border-bottom: 1px solid #f9fafb; font-size: .88rem; color: #374151; }
.table-modern tbody tr:last-child td { border-bottom: none; }
.level-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #fff7ed; color: #ea580c;
    border-radius: 8px; padding: 5px 12px; font-weight: 700; font-size: .84rem;
    border: 1px solid #fed7aa;
}
.module-count-pill {
    display: inline-flex; align-items: center; gap: 5px;
    background: #eff6ff; color: #2563eb;
    border-radius: 20px; padding: 4px 12px; font-size: .78rem; font-weight: 700;
}
.action-btn {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 8px; border: 1.5px solid transparent;
    font-size: .82rem; cursor: pointer; transition: all .15s;
    text-decoration: none; background: transparent;
}
.action-btn-view  { color: #f97316; border-color: #fed7aa; background: #fff7ed; }
.action-btn-view:hover  { background: #fdba74; color: #c2410c; }
.action-btn-edit  { color: #d97706; border-color: #fef3c7; background: #fffbeb; }
.action-btn-edit:hover  { background: #fde68a; color: #b45309; }
.action-btn-delete { color: #dc2626; border-color: #fee2e2; background: #fff5f5; }
.action-btn-delete:hover { background: #fecaca; color: #b91c1c; }
.empty-hierarchy { padding: 60px 20px; text-align: center; }
.empty-hierarchy .empty-icon {
    width: 72px; height: 72px; background: #fff7ed; border-radius: 18px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px; font-size: 1.8rem; color: #ea580c;
}
.empty-hierarchy h6 { font-weight: 700; color: #1f2937; margin-bottom: 6px; }
.empty-hierarchy p { color: #9ca3af; font-size: .88rem; margin-bottom: 20px; }

/* View modal */
.modal-content { border: 0; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.18); overflow: hidden; }
.modal-header { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: #fff; border: none; padding: 20px 24px; }
.modal-header .modal-title { font-weight: 700; font-size: 1rem; }
.modal-header .close { color: #fff; opacity: .8; font-size: 1.4rem; }
.modal-body { padding: 24px; }
.modal-footer { border-top: 1px solid #f3f4f6; padding: 16px 24px; background: #fafafa; }
.detail-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 72px; height: 72px; border-radius: 18px;
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: #fff; font-size: 1.5rem; font-weight: 800; margin: 0 auto 12px;
}
.perm-pill { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; }
.perm-pill-view    { background: #dcfce7; color: #16a34a; }
.perm-pill-create  { background: #dbeafe; color: #2563eb; }
.perm-pill-edit    { background: #fef3c7; color: #d97706; }
.perm-pill-delete  { background: #fee2e2; color: #dc2626; }
.perm-pill-approve { background: #cffafe; color: #0891b2; }
.mod-item-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 14px; border-radius: 10px; margin-bottom: 6px;
    background: #fafafa; border: 1px solid #f3f4f6;
}
.mod-item-name { font-size: .85rem; font-weight: 600; color: #1f2937; }
.accordion-button { font-weight: 600; font-size: .88rem; }

/* SweetAlert orange theme overrides */
.swal2-confirm.swal-btn-orange { background: linear-gradient(135deg, #f97316, #ea580c) !important; border: none !important; }
.swal2-confirm.swal-btn-orange:hover { filter: brightness(1.08); }
</style>

<div class="content container-fluid" style="padding-top:20px;">

    <!-- Page Header -->
    <div class="hierarchy-page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item active">Hierarchy Management</li>
                    </ol>
                </nav>
                <h1><i class="fas fa-sitemap me-2 opacity-75"></i>Hierarchy Management</h1>
                <p>Define role levels and configure module access permissions for your organisation.</p>
            </div>
            <a href="{{ route('hierarchy.create') }}" class="btn-header-action">
                <i class="fas fa-plus"></i>Add Hierarchy Level
            </a>
        </div>
    </div>

    <!-- Flash messages via SweetAlert -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('success') }}',
                timer: 3000, timerProgressBar: true, showConfirmButton: false,
                toast: true, position: 'top-end' });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            Swal.fire({ icon: 'error', title: 'Error', text: '{{ session('error') }}',
                timer: 4000, timerProgressBar: true, showConfirmButton: false,
                toast: true, position: 'top-end' });
        });
    </script>
    @endif

    <!-- Filter Card -->
    <div class="card filter-card">
        <div class="card-body">
            <div class="row align-items-center" style="gap:0;">
                <div class="col-md-8">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white" style="border-radius:10px 0 0 10px; border:1.5px solid #e5e7eb; border-right:0;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="searchHierarchy" class="form-control"
                               placeholder="Search hierarchy levels…"
                               style="border-radius:0 10px 10px 0; border:1.5px solid #e5e7eb; border-left:0;">
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn btn-filter-refresh" onclick="location.reload()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card hierarchy-table-card">
        <div class="card-header">
            <h5><i class="fas fa-layer-group mr-2" style="color:#f97316;"></i>Hierarchy Levels</h5>
            <span class="badge" style="background:#fff7ed;color:#f97316;font-size:.78rem;padding:5px 12px;border-radius:20px;">
                {{ count($hierarchies) }} {{ Str::plural('Level', count($hierarchies)) }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="hierarchyTable" class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width:52px;">#</th>
                            <th>Hierarchy Level</th>
                            <th>Module Access</th>
                            <th>Created</th>
                            <th class="text-right" style="padding-right:24px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hierarchies as $index => $hierarchy)
                        <tr id="row-{{ $hierarchy->id }}">
                            <td style="color:#9ca3af;font-weight:600;">{{ $index + 1 }}</td>
                            <td>
                                <span class="level-badge">
                                    <i class="fas fa-layer-group"></i>
                                    {{ $hierarchy->hierarchy_level }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $mods = json_decode($hierarchy->modules ?? '[]', true);
                                    $mc = is_array($mods) ? count($mods) : 0;
                                @endphp
                                <span class="module-count-pill">
                                    <i class="fas fa-puzzle-piece"></i>
                                    {{ $mc }} {{ Str::plural('Module', $mc) }}
                                </span>
                            </td>
                            <td style="color:#9ca3af;font-size:.82rem;">
                                {{ \Carbon\Carbon::parse($hierarchy->created_at)->format('d M Y') }}
                            </td>
                            <td class="text-right" style="padding-right:20px;">
                                <div class="d-flex align-items-center justify-content-end" style="gap:4px;">
                                    <button class="action-btn action-btn-view" title="View Details"
                                            onclick="viewHierarchy({{ $hierarchy->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('hierarchy.edit', $hierarchy->id) }}"
                                       class="action-btn action-btn-edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button class="action-btn action-btn-delete" title="Delete"
                                            onclick="confirmDelete({{ $hierarchy->id }}, '{{ addslashes($hierarchy->hierarchy_level) }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-0">
                                <div class="empty-hierarchy">
                                    <div class="empty-icon"><i class="fas fa-sitemap"></i></div>
                                    <h6>No Hierarchy Levels Found</h6>
                                    <p>Create your first hierarchy level to define role access.</p>
                                    <a href="{{ route('hierarchy.create') }}" class="btn btn-primary" style="border-radius:10px;">
                                        <i class="fas fa-plus mr-2"></i>Create Hierarchy Level
                                    </a>
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

<!-- View Details Modal (BS4 compatible) -->
<div class="modal fade" id="hierarchyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sitemap mr-2"></i>Hierarchy Level Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="hierarchyModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-3 text-muted">Loading details…</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:10px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
/* ── Named Routes ── */
var HRoutes = {
    index:   '{{ route("hierarchy.index") }}',
    create:  '{{ route("hierarchy.create") }}',
    destroy: '{{ url("hierarchy") }}',
    details: '{{ url("hierarchy") }}'
};

/* ── Search ── */
document.getElementById('searchHierarchy').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#hierarchyTable tbody tr').forEach(function(row) {
        if (row.cells.length === 1) return;
        row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
    });
});

/* ── View Hierarchy Details ── */
function viewHierarchy(id) {
    document.getElementById('hierarchyModalBody').innerHTML =
        '<div class="text-center py-5"><div class="spinner-border text-warning" role="status"></div><p class="mt-3 text-muted">Loading…</p></div>';

    $('#hierarchyModal').modal('show');

    fetch(HRoutes.details + '/' + id + '/details')
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (data.error) throw new Error(data.error);
            var h = data.hierarchy;
            var modules = h.modules || {};
            var mKeys = Object.keys(modules);

            function permPill(perms, key, cls, icon, label) {
                return perms[key] ? '<span class="perm-pill perm-pill-' + cls + '"><i class="' + icon + '"></i>' + label + '</span>' : '';
            }

            var categoryMap = {
                'Recruitment': [], 'Leaves': [], 'Attendance': [],
                'Projects': [], 'Employee': [], 'Shifts & Schedule': [],
                'Payroll': [], 'Support': [], 'Finance': [],
                'Reports': [], 'HR Tools': [], 'Settings': [], 'Other': []
            };
            function catOf(name) {
                if (name.indexOf('Recruitment') > -1) return 'Recruitment';
                if (name.indexOf('Leave') > -1 || name.indexOf('Leave') > -1) return 'Leaves';
                if (name.indexOf('Attendance') > -1 || name.indexOf('Late Punch') > -1) return 'Attendance';
                if (name.indexOf('Client') > -1 || name.indexOf('Project') > -1 || name.indexOf('Task') > -1) return 'Projects';
                if (name.indexOf('Employee') > -1) return 'Employee';
                if (name.indexOf('Shift') > -1 || name.indexOf('Schedule') > -1) return 'Shifts & Schedule';
                if (name.indexOf('Payroll') > -1 || name.indexOf('Salary') > -1 || name.indexOf('Payslip') > -1 || name.indexOf('Activity Log') > -1) return 'Payroll';
                if (name.indexOf('Ticket') > -1 || name.indexOf('Testing') > -1) return 'Support';
                if (name.indexOf('Estimate') > -1 || name.indexOf('Invoice') > -1 || name.indexOf('Payment') > -1 || name.indexOf('Expense') > -1 || name.indexOf('Budget') > -1 || name.indexOf('Tax') > -1 || name.indexOf('Categor') > -1) return 'Finance';
                if (name.indexOf('Report') > -1) return 'Reports';
                if (name.indexOf('Policy') > -1 || name.indexOf('Goal') > -1 || name.indexOf('Asset') > -1 || name.indexOf('Training') > -1 || name.indexOf('Travel') > -1 || name.indexOf('Offboard') > -1) return 'HR Tools';
                if (name.indexOf('Setting') > -1) return 'Settings';
                return 'Other';
            }
            mKeys.forEach(function(n) {
                var cat = catOf(n);
                if (categoryMap[cat]) categoryMap[cat].push(n);
                else categoryMap['Other'].push(n);
            });

            var html = '<div class="text-center mb-4">' +
                '<div class="detail-badge">' + h.hierarchy_level.substring(0,2).toUpperCase() + '</div>' +
                '<h5 class="font-weight-bold mb-1">' + h.hierarchy_level + '</h5>' +
                '<div class="d-flex justify-content-center text-muted" style="gap:16px;font-size:.82rem;">' +
                '<span><i class="fas fa-calendar mr-1"></i>Created ' + new Date(h.created_at).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) + '</span>' +
                '<span><i class="fas fa-puzzle-piece mr-1"></i>' + mKeys.length + ' modules</span>' +
                '</div></div>';

            if (mKeys.length === 0) {
                html += '<p class="text-center text-muted py-4">No modules assigned.</p>';
            } else {
                html += '<div id="modAccordion" role="tablist">';
                var idx = 0;
                Object.keys(categoryMap).forEach(function(cat) {
                    var names = categoryMap[cat];
                    if (!names.length) return;
                    var colId = 'cat' + idx;
                    html += '<div class="card mb-2" style="border-radius:10px;overflow:hidden;">' +
                        '<div class="card-header" role="tab" style="background:#f9fafb;padding:0;">' +
                        '<button class="btn btn-block text-left font-weight-bold d-flex align-items-center justify-content-between" style="padding:12px 16px;font-size:.88rem;" data-toggle="collapse" data-target="#' + colId + '">' +
                        '<span>' + cat + '</span>' +
                        '<span class="badge" style="background:#fff7ed;color:#f97316;">' + names.length + '</span>' +
                        '</button></div>' +
                        '<div id="' + colId + '" class="collapse ' + (idx === 0 ? 'show' : '') + '" data-parent="#modAccordion">' +
                        '<div class="card-body pt-2">';
                    names.forEach(function(n) {
                        var p = modules[n] || {};
                        html += '<div class="mod-item-row">' +
                            '<span class="mod-item-name"><i class="fas fa-check-circle mr-2 text-success" style="font-size:.8rem;"></i>' + n + '</span>' +
                            '<div class="d-flex flex-wrap" style="gap:4px;">' +
                            permPill(p,'view','view','fas fa-eye mr-1','View') +
                            permPill(p,'create','create','fas fa-plus mr-1','Create') +
                            permPill(p,'edit','edit','fas fa-pencil-alt mr-1','Edit') +
                            permPill(p,'delete','delete','fas fa-trash mr-1','Delete') +
                            permPill(p,'approve','approve','fas fa-check mr-1','Approve') +
                            '</div></div>';
                    });
                    html += '</div></div></div>';
                    idx++;
                });
                html += '</div>';
            }
            document.getElementById('hierarchyModalBody').innerHTML = html;
        })
        .catch(function(err) {
            document.getElementById('hierarchyModalBody').innerHTML =
                '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error: ' + err.message + '</div>';
        });
}

/* ── Delete with SweetAlert2 ── */
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Delete Hierarchy?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small class="text-muted">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        focusCancel: true
    }).then(function(result) {
        if (!result.isConfirmed) return;

        Swal.fire({
            title: 'Deleting…',
            text: 'Please wait.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: function() { Swal.showLoading(); }
        });

        fetch(HRoutes.destroy + '/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(function(r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(function(data) {
            if (!data.success) throw new Error(data.message || 'Delete failed');
            var row = document.getElementById('row-' + id);
            if (row) row.remove();
            Swal.fire({
                icon: 'success', title: 'Deleted!',
                text: name + ' has been removed.',
                timer: 2500, timerProgressBar: true,
                showConfirmButton: false,
                toast: true, position: 'top-end'
            });
        })
        .catch(function(err) {
            Swal.fire({
                icon: 'error', title: 'Error',
                text: err.message,
                confirmButtonColor: '#f97316'
            });
        });
    });
}
</script>
@endsection
