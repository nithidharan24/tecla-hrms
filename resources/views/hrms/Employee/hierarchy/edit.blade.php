@php
    $company = DB::table('subcompany')->first();
    $planId = $company->plan_id ?? null;
    $allowedModules = [];
    if ($planId) {
        $allowedModules = DB::table('plan_modules')
            ->join('modules', 'plan_modules.module_id', '=', 'modules.id')
            ->where('plan_modules.plan_id', $planId)
            ->pluck('modules.name')
            ->toArray();
    }
 
    $decodedModules = [];
    if (is_array($hierarchy->modules)) {
        $decodedModules = $hierarchy->modules;
    } else {
        $decodedModules = json_decode($hierarchy->modules ?? '{}', true);
    }
    if (!is_array($decodedModules)) { $decodedModules = []; }

    function isPermissionChecked($permissions, $key) {
        return isset($permissions[$key]) && ($permissions[$key] === true || $permissions[$key] === 1 || $permissions[$key] === '1');
    }
@endphp
@extends('layouts.index')
@section('content')
<style>
/* ── Hierarchy Builder (hb-) ── */
:root {
    --hb-orange: #f97316;
    --hb-orange-dark: #ea580c;
    --hb-orange-light: #fff7ed;
    --hb-orange-mid: #fed7aa;
    --hb-sidebar-w: 290px;
    --hb-radius: 12px;
    --hb-transition: 0.2s ease;
}
.hb-wrap { display: flex; gap: 0; min-height: calc(100vh - 120px); }
.hb-sidebar {
    width: var(--hb-sidebar-w);
    min-width: var(--hb-sidebar-w);
    background: #fff;
    border-right: 1px solid #f1f5f9;
    display: flex;
    flex-direction: column;
    border-radius: var(--hb-radius) 0 0 var(--hb-radius);
    box-shadow: 2px 0 12px rgba(0,0,0,.04);
    position: sticky;
    top: 70px;
    max-height: calc(100vh - 90px);
    overflow: hidden;
}
.hb-sidebar-top {
    padding: 20px 18px 14px;
    background: linear-gradient(135deg, var(--hb-orange) 0%, var(--hb-orange-dark) 100%);
    color: #fff;
}
.hb-sidebar-top h6 { font-size: 11px; letter-spacing: .08em; text-transform: uppercase; opacity: .85; margin-bottom: 10px; }
.hb-name-input {
    width: 100%;
    border: 2px solid rgba(255,255,255,.35);
    border-radius: 8px;
    background: rgba(255,255,255,.18);
    color: #fff;
    padding: 9px 12px;
    font-size: 14px;
    font-weight: 600;
    outline: none;
    transition: var(--hb-transition);
}
.hb-name-input::placeholder { color: rgba(255,255,255,.65); }
.hb-name-input:focus { border-color: rgba(255,255,255,.85); background: rgba(255,255,255,.28); }
.hb-stats { display: flex; gap: 8px; margin-top: 12px; }
.hb-stat {
    flex: 1;
    background: rgba(255,255,255,.2);
    border-radius: 8px;
    padding: 8px 10px;
    text-align: center;
}
.hb-stat-num { font-size: 18px; font-weight: 800; display: block; line-height: 1; }
.hb-stat-lbl { font-size: 10px; opacity: .8; margin-top: 2px; display: block; }
.hb-quick-actions { padding: 12px 18px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 8px; }
.hb-qa-btn {
    flex: 1;
    font-size: 11px;
    padding: 6px 8px;
    border-radius: 6px;
    border: 1.5px solid var(--hb-orange);
    background: transparent;
    color: var(--hb-orange);
    cursor: pointer;
    font-weight: 600;
    transition: var(--hb-transition);
    text-align: center;
}
.hb-qa-btn:hover { background: var(--hb-orange); color: #fff; }
.hb-cat-nav { flex: 1; overflow-y: auto; padding: 8px 0; }
.hb-cat-nav::-webkit-scrollbar { width: 4px; }
.hb-cat-nav::-webkit-scrollbar-thumb { background: var(--hb-orange-mid); border-radius: 4px; }
.hb-cat-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    cursor: pointer;
    transition: var(--hb-transition);
    border-left: 3px solid transparent;
}
.hb-cat-item:hover { background: var(--hb-orange-light); }
.hb-cat-item.active { background: var(--hb-orange-light); border-left-color: var(--hb-orange); }
.hb-cat-item.active .hb-cat-icon { background: var(--hb-orange); color: #fff; }
.hb-cat-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    color: #64748b;
    flex-shrink: 0;
    transition: var(--hb-transition);
}
.hb-cat-label { font-size: 13px; font-weight: 600; color: #374151; flex: 1; }
.hb-cat-badge {
    font-size: 10px;
    background: var(--hb-orange-mid);
    color: var(--hb-orange-dark);
    border-radius: 12px;
    padding: 2px 7px;
    font-weight: 700;
}
.hb-cat-item.active .hb-cat-label { color: var(--hb-orange-dark); }
.hb-sidebar-footer { padding: 14px 18px; border-top: 1px solid #f1f5f9; }
.hb-save-btn {
    width: 100%;
    background: linear-gradient(135deg, var(--hb-orange) 0%, var(--hb-orange-dark) 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 11px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: var(--hb-transition);
    letter-spacing: .02em;
}
.hb-save-btn:hover { filter: brightness(1.08); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(249,115,22,.35); }
.hb-content {
    flex: 1;
    background: #f8fafc;
    padding: 24px;
    border-radius: 0 var(--hb-radius) var(--hb-radius) 0;
    overflow-y: auto;
    max-height: calc(100vh - 90px);
}
.hb-panel { display: none; }
.hb-panel.active { display: block; }
.hb-panel-header { margin-bottom: 20px; }
.hb-panel-header h4 { font-size: 18px; font-weight: 800; color: #1e293b; margin-bottom: 4px; }
.hb-panel-header p { font-size: 13px; color: #64748b; margin: 0; }
.hb-module-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
.hb-mod-card {
    background: #fff;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    transition: var(--hb-transition);
    overflow: hidden;
}
.hb-mod-card.enabled { border-color: var(--hb-orange); box-shadow: 0 0 0 3px rgba(249,115,22,.1); }
.hb-mod-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    cursor: pointer;
}
.hb-mod-info { display: flex; align-items: center; gap: 10px; }
.hb-mod-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    color: #64748b;
    transition: var(--hb-transition);
}
.hb-mod-card.enabled .hb-mod-icon { background: var(--hb-orange-light); color: var(--hb-orange); }
.hb-mod-name { font-size: 13px; font-weight: 700; color: #1e293b; }
.hb-mod-sub  { font-size: 11px; color: #94a3b8; margin-top: 1px; }
.hb-toggle-wrap { position: relative; display: inline-block; cursor: pointer; }
.hb-toggle-wrap input { position: absolute; opacity: 0; width: 0; height: 0; }
.hb-toggle-track {
    display: block;
    width: 42px; height: 22px;
    border-radius: 11px;
    background: #cbd5e1;
    transition: var(--hb-transition);
    position: relative;
}
.hb-toggle-thumb {
    position: absolute;
    top: 3px; left: 3px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: #fff;
    transition: var(--hb-transition);
    box-shadow: 0 1px 4px rgba(0,0,0,.2);
}
.hb-toggle-wrap input:checked ~ .hb-toggle-track { background: var(--hb-orange); }
.hb-toggle-wrap input:checked ~ .hb-toggle-track .hb-toggle-thumb { left: 23px; }
.hb-perms { padding: 0 16px 14px; display: none; border-top: 1px solid #f1f5f9; }
.hb-perms.show { display: block; }
.hb-perms-label { font-size: 11px; font-weight: 700; color: #94a3b8; letter-spacing: .07em; text-transform: uppercase; margin: 10px 0 8px; }
.hb-perms-row { display: flex; flex-wrap: wrap; gap: 6px; }
.hb-pp { display: inline-flex; align-items: center; cursor: pointer; }
.hb-pp input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }
.hb-pp-inner {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: 20px;
    border: 1.5px solid #e2e8f0;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #fff;
    transition: var(--hb-transition);
    user-select: none;
}
.hb-pp input:checked + .hb-pp-inner {
    background: var(--hb-orange);
    border-color: var(--hb-orange);
    color: #fff;
}
.hb-pp-inner i { font-size: 10px; }
.hb-page-header {
    background: linear-gradient(135deg, var(--hb-orange) 0%, var(--hb-orange-dark) 100%);
    border-radius: var(--hb-radius);
    padding: 20px 24px;
    color: #fff;
    margin-bottom: 20px;
}
.hb-page-header h3 { font-size: 20px; font-weight: 800; margin: 0 0 4px; }
.hb-page-header p { margin: 0; opacity: .85; font-size: 13px; }
.hb-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; opacity: .8; margin-bottom: 8px; }
.hb-breadcrumb a { color: rgba(255,255,255,.85); text-decoration: none; }
.hb-breadcrumb a:hover { color: #fff; }
.hb-breadcrumb span { opacity: .6; }
.hb-back-btn {
    background: rgba(255,255,255,.2);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: var(--hb-transition);
}
.hb-back-btn:hover { background: rgba(255,255,255,.35); color: #fff; }
.hb-name-input.is-invalid { border-color: #ef4444 !important; }
.hb-err { font-size: 11px; color: #fecaca; margin-top: 4px; display: none; }
</style>

<div class="content container-fluid" style="padding-top:16px;">

    <!-- Page Header -->
    <div class="hb-page-header d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <div class="hb-breadcrumb">
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <a href="{{ route('hierarchy.index') }}">Hierarchy</a>
                <span>/</span>
                <span style="color:#fff;">Edit</span>
            </div>
            <h3><i class="fas fa-pencil-alt me-2"></i>Edit Hierarchy Level</h3>
            <p>Update the role level name and module access permissions below.</p>
        </div>
        <a href="{{ route('hierarchy.index') }}" class="hb-back-btn">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="fas fa-times-circle me-2"></i>
            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form id="hbForm" method="POST" action="{{ route('hierarchy.update', $hierarchy->id) }}" novalidate>
        @csrf
        @method('PUT')

        <div class="hb-wrap">

            <!-- ── LEFT SIDEBAR ── -->
            <div class="hb-sidebar">
                <div class="hb-sidebar-top">
                    <h6>Hierarchy Level Name</h6>
                    <input type="text" name="hierarchy_level" id="hbLevelName" class="hb-name-input @error('hierarchy_level') is-invalid @enderror"
                           placeholder="e.g. Manager, HR Admin…"
                           value="{{ old('hierarchy_level', $hierarchy->hierarchy_level) }}" required>
                    <div class="hb-err" id="hbNameErr">Level name is required.</div>
                    <div class="hb-stats">
                        <div class="hb-stat">
                            <span class="hb-stat-num" id="statModules">0</span>
                            <span class="hb-stat-lbl">Modules</span>
                        </div>
                        <div class="hb-stat">
                            <span class="hb-stat-num" id="statPerms">0</span>
                            <span class="hb-stat-lbl">Permissions</span>
                        </div>
                    </div>
                </div>

                <div class="hb-quick-actions">
                    <button type="button" class="hb-qa-btn" id="btnSelectAll">
                        <i class="fas fa-check-double me-1"></i>Enable All
                    </button>
                    <button type="button" class="hb-qa-btn" id="btnClearAll">
                        <i class="fas fa-times me-1"></i>Clear All
                    </button>
                </div>

                <nav class="hb-cat-nav" id="hbCatNav"></nav>

                <div class="hb-sidebar-footer">
                    <button type="submit" class="hb-save-btn">
                        <i class="fas fa-save me-2"></i>Update Hierarchy Level
                    </button>
                </div>
            </div>

            <!-- ── RIGHT CONTENT ── -->
            <div class="hb-content" id="hbContent"></div>

        </div>
    </form>

</div>

<script>
(function(){
"use strict";

const CATEGORIES = [
    {
        id: "recruitment", label: "Recruitment", icon: "fas fa-user-plus",
        modules: [
            { key: "Recruitment Overview", label: "Overview", icon: "fas fa-chart-line", perms: ["view"] },
            { key: "Job Vacancy Requests", label: "Job Vacancy Requests", icon: "fas fa-clipboard-list", perms: ["view","create","edit","delete","approve"] },
            { key: "Job Listings", label: "Job Listings", icon: "fas fa-briefcase", perms: ["view","create","edit","delete"] },
            { key: "Candidates Management", label: "Candidates", icon: "fas fa-users", perms: ["view","create","edit","delete"] },
            { key: "Send Offer Letter", label: "Send Offer Letter", icon: "fas fa-envelope-open-text", perms: ["view","create","approve"] },
            { key: "Questions & Experience Management", label: "Questions & Experience Management", icon: "fas fa-question-circle", perms: ["view","create","edit","delete"] },
            { key: "Recruitment Activity Log", label: "Activity Log", icon: "fas fa-history", perms: ["view"] }
        ]
    },
    {
        id: "leaves", label: "Leaves", icon: "fas fa-calendar-times",
        modules: [
            { key: "Team Leaves",     label: "Team Leaves",     icon: "fas fa-users",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Employee Leaves", label: "Employee Leaves", icon: "fas fa-calendar-times",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "attendance", label: "Attendance", icon: "fas fa-clock",
        modules: [
            { key: "Admin Attendance",    label: "Admin Attendance",    icon: "fas fa-user-clock",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Employee Attendance", label: "Employee Attendance", icon: "fas fa-clock",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Late Punch Approval", label: "Late Punch Approval", icon: "fas fa-stamp",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "projects", label: "Projects", icon: "fas fa-project-diagram",
        modules: [
            { key: "Clients",       label: "Clients",       icon: "fas fa-building",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Projects",      label: "Projects",      icon: "fas fa-project-diagram",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Project Tasks", label: "Project Tasks", icon: "fas fa-tasks",
              perms: ["view","create","edit","delete","approve"] },
            { key: "My Tasks",      label: "My Tasks",      icon: "fas fa-check-square",
              perms: ["view"] }
        ]
    },
    {
        id: "employee", label: "Employee", icon: "fas fa-id-badge",
        modules: [
            { key: "Employee", label: "Employee Management", icon: "fas fa-id-badge",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "shifts", label: "Shifts & Schedule", icon: "fas fa-calendar-alt",
        modules: [
            { key: "Manage Shifts", label: "Manage Shifts", icon: "fas fa-exchange-alt",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Schedule",      label: "Schedule",      icon: "fas fa-calendar-check",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "payroll", label: "Payroll", icon: "fas fa-money-bill-wave",
        modules: [
            { key: "Payroll Items",      label: "Payroll Items",      icon: "fas fa-list-alt",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Employee Salary",    label: "Employee Salary",    icon: "fas fa-dollar-sign",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Automated Payslips", label: "Automated Payslips", icon: "fas fa-file-invoice-dollar",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Activity Log",       label: "Activity Log",       icon: "fas fa-history",
              perms: ["view"] }
        ]
    },
    {
        id: "support", label: "Support", icon: "fas fa-headset",
        modules: [
            { key: "Tickets", label: "Tickets", icon: "fas fa-ticket-alt",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Testing", label: "Testing", icon: "fas fa-vial",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "finance", label: "Finance", icon: "fas fa-coins",
        modules: [
            { key: "Estimates",       label: "Estimates",       icon: "fas fa-file-alt",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Invoices",        label: "Invoices",        icon: "fas fa-file-invoice",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Payments",        label: "Payments",        icon: "fas fa-credit-card",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Expenses",        label: "Expenses",        icon: "fas fa-receipt",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Taxes",           label: "Taxes",           icon: "fas fa-percentage",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Categories",      label: "Categories",      icon: "fas fa-tags",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Budgets",         label: "Budgets",         icon: "fas fa-chart-pie",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Budget Expenses", label: "Budget Expenses", icon: "fas fa-minus-circle",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Budget Revenues", label: "Budget Revenues", icon: "fas fa-plus-circle",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "reports", label: "Reports", icon: "fas fa-chart-bar",
        modules: [
            { key: "My Reports",           label: "My Reports",           icon: "fas fa-user-chart",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Team Reports",         label: "Team Reports",         icon: "fas fa-users",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Organization Reports", label: "Organization Reports", icon: "fas fa-building",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "hr", label: "HR Tools", icon: "fas fa-briefcase",
        modules: [
            { key: "Policy",      label: "Policy",      icon: "fas fa-scroll",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Goals",       label: "Goals",       icon: "fas fa-bullseye",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Assets",      label: "Assets",      icon: "fas fa-laptop",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Training",    label: "Training",    icon: "fas fa-graduation-cap",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Travel",      label: "Travel",      icon: "fas fa-plane",
              perms: ["view","create","edit","delete","approve"] },
            { key: "Offboarding", label: "Offboarding", icon: "fas fa-door-open",
              perms: ["view","create","edit","delete","approve"] }
        ]
    },
    {
        id: "settings", label: "Settings", icon: "fas fa-cog",
        modules: [
            { key: "Settings", label: "Settings", icon: "fas fa-cog",
              perms: ["view","edit"],
              permLabels: { view: "View", edit: "Edit" },
              permIcons:  { view: "fa-eye", edit: "fa-pencil-alt" } }
        ]
    }
];

const PERM_META = {
    view:    { label: "View",    icon: "fa-eye" },
    create:  { label: "Create",  icon: "fa-plus" },
    edit:    { label: "Edit",    icon: "fa-pencil-alt" },
    delete:  { label: "Delete",  icon: "fa-trash" },
    approve: { label: "Approve", icon: "fa-check-circle" }
};

const SAVED = @json($decodedModules);

function savedVal(modKey, field) {
    if (!SAVED || !SAVED[modKey]) return false;
    const v = SAVED[modKey][field];
    return (v === true || v === 1 || v === '1');
}

const nav     = document.getElementById('hbCatNav');
const content = document.getElementById('hbContent');

CATEGORIES.forEach(function(cat, ci) {
    const item = document.createElement('div');
    item.className = 'hb-cat-item' + (ci === 0 ? ' active' : '');
    item.dataset.cat = cat.id;
    item.innerHTML = `
        <span class="hb-cat-icon"><i class="${cat.icon}"></i></span>
        <span class="hb-cat-label">${cat.label}</span>
        <span class="hb-cat-badge" id="badge-${cat.id}">0</span>
    `;
    item.addEventListener('click', function(){ showPanel(cat.id); });
    nav.appendChild(item);

    const panel = document.createElement('div');
    panel.className = 'hb-panel' + (ci === 0 ? ' active' : '');
    panel.id = 'panel-' + cat.id;

    let grid = `<div class="hb-panel-header">
        <h4><i class="${cat.icon} me-2" style="color:var(--hb-orange)"></i>${cat.label}</h4>
        <p>Toggle modules on and select permissions for each.</p>
    </div><div class="hb-module-grid">`;

    cat.modules.forEach(function(mod) {
        const safeId = mod.key.replace(/[^a-zA-Z0-9]/g, '_');
        /* Module was enabled if its key exists in saved data */
        const isEnabled     = SAVED && SAVED[mod.key] !== undefined && SAVED[mod.key] !== null;
        const enabledClass  = isEnabled ? ' enabled' : '';
        const showPerms     = isEnabled ? ' show' : '';
        const checkedToggle = isEnabled ? 'checked' : '';

        let pills = '';
        mod.perms.forEach(function(p) {
            const labelStr = (mod.permLabels && mod.permLabels[p]) ? mod.permLabels[p] : (PERM_META[p] ? PERM_META[p].label : p);
            const iconStr  = (mod.permIcons && mod.permIcons[p]) ? mod.permIcons[p] : (PERM_META[p] ? PERM_META[p].icon : 'fa-check');
            const isChecked = (isEnabled && SAVED[mod.key][p] == 1) ? 'checked' : '';
            pills += `<label class="hb-pp">
                <input type="checkbox" name="modules[${mod.key}][${p}]" value="1" data-mod="${safeId}" ${isChecked}>
                <span class="hb-pp-inner"><i class="fas ${iconStr}"></i> ${labelStr}</span>
            </label>`;
        });
        const permHtml = `<div class="hb-perms${showPerms}" id="perms-${safeId}">
            <div class="hb-perms-label">Permissions</div>
            <div class="hb-perms-row">${pills}</div>
        </div>`;

        grid += `<div class="hb-mod-card${enabledClass}" id="card-${safeId}">
            <div class="hb-mod-header">
                <div class="hb-mod-info">
                    <span class="hb-mod-icon"><i class="${mod.icon}"></i></span>
                    <div>
                        <div class="hb-mod-name">${mod.label}</div>
                        <div class="hb-mod-sub">${mod.perms.length + ' permissions'}</div>
                    </div>
                </div>
                <label class="hb-toggle-wrap" onclick="event.stopPropagation()">
                    <input type="checkbox" name="modules[${mod.key}][enabled]" value="1"
                           id="toggle-${safeId}" class="module-toggle" data-mod="${safeId}" data-cat="${cat.id}" ${checkedToggle}>
                    <span class="hb-toggle-track"><span class="hb-toggle-thumb"></span></span>
                </label>
            </div>
            ${permHtml}
        </div>`;
    });

    grid += '</div>';
    panel.innerHTML = grid;
    content.appendChild(panel);
});

function showPanel(catId) {
    document.querySelectorAll('.hb-cat-item').forEach(function(el){ el.classList.remove('active'); });
    document.querySelectorAll('.hb-panel').forEach(function(el){ el.classList.remove('active'); });
    const navItem = document.querySelector('.hb-cat-item[data-cat="' + catId + '"]');
    const panel   = document.getElementById('panel-' + catId);
    if (navItem) navItem.classList.add('active');
    if (panel)   panel.classList.add('active');
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('module-toggle')) {
        const safeId = e.target.dataset.mod;
        const card   = document.getElementById('card-' + safeId);
        const perms  = document.getElementById('perms-' + safeId);
        if (e.target.checked) {
            card.classList.add('enabled');
            if (perms) perms.classList.add('show');
        } else {
            card.classList.remove('enabled');
            if (perms) {
                perms.classList.remove('show');
                perms.querySelectorAll('input[type="checkbox"]').forEach(function(cb){ cb.checked = false; });
            }
        }
        updateStats();
        updateBadge(e.target.dataset.cat);
    }
    if (e.target.dataset.mod && !e.target.classList.contains('module-toggle')) {
        updateStats();
    }
});

function updateStats() {
    const mods  = document.querySelectorAll('.module-toggle:checked').length;
    const perms = document.querySelectorAll('.hb-perms input[type="checkbox"]:checked').length;
    document.getElementById('statModules').textContent = mods;
    document.getElementById('statPerms').textContent   = perms;
}

function updateBadge(catId) {
    const cat = CATEGORIES.find(function(c){ return c.id === catId; });
    if (!cat) return;
    const enabled = cat.modules.filter(function(m) {
        const safeId = m.key.replace(/[^a-zA-Z0-9]/g, '_');
        const t = document.getElementById('toggle-' + safeId);
        return t && t.checked;
    }).length;
    const badge = document.getElementById('badge-' + catId);
    if (badge) badge.textContent = enabled;
}

/* Initialise stats + badges from saved state */
(function initStats() {
    updateStats();
    CATEGORIES.forEach(function(cat){ updateBadge(cat.id); });
})();

document.getElementById('btnSelectAll').addEventListener('click', function() {
    document.querySelectorAll('.module-toggle').forEach(function(cb) {
        if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change', { bubbles: true })); }
    });
    document.querySelectorAll('.hb-perms input[type="checkbox"]').forEach(function(cb) {
        cb.checked = true;
    });
    updateStats();
});
document.getElementById('btnClearAll').addEventListener('click', function() {
    document.querySelectorAll('.module-toggle').forEach(function(cb) {
        if (cb.checked) { cb.checked = false; cb.dispatchEvent(new Event('change', { bubbles: true })); }
    });
});

document.getElementById('hbForm').addEventListener('submit', function(e) {
    const name = document.getElementById('hbLevelName');
    const err  = document.getElementById('hbNameErr');
    if (!name.value.trim()) {
        e.preventDefault();
        name.classList.add('is-invalid');
        err.style.display = 'block';
        name.focus();
    }
});
document.getElementById('hbLevelName').addEventListener('input', function() {
    this.classList.remove('is-invalid');
    document.getElementById('hbNameErr').style.display = 'none';
});

})();
</script>
@endsection
