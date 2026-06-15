<style>
/* Modern Statistics Cards */
.stats-row {
    margin-bottom: 25px;
}

.stats-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #f0f0f0;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #fff;
    flex-shrink: 0;
}

.stats-icon.bg-gradient-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-icon.bg-gradient-green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stats-icon.bg-gradient-orange {
    background: linear-gradient(135deg, #ffa726 0%, #fb8c00 100%);
}

.stats-icon.bg-gradient-blue {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-icon.bg-gradient-red {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stats-icon.bg-gradient-cyan {
    background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
}

.stats-content {
    flex-grow: 1;
}

.stats-content h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stats-content p {
    margin: 0;
    color: #7f8c8d;
    font-size: 14px;
    font-weight: 500;
}

/* Modern Filter Design */
.modern-filter-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    border: 1px solid #f0f0f0;
    padding: 20px;
}

.modern-filter-card .form-control,
.modern-filter-card .form-select,
.modern-filter-card select {
    border: 1.5px solid #e4e6ef;
    border-radius: 8px;
    padding: 10px 15px;
    font-size: 14px;
    transition: all 0.3s;
    background: #fff;
}

.modern-filter-card .form-control:focus,
.modern-filter-card .form-select:focus,
.modern-filter-card select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.1);
    outline: none;
}

.modern-filter-card .form-control::placeholder {
    color: #a8a8a8;
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.btn-filter {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #fff;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    color: #fff;
}

.btn-reset-filter {
    background: #f8f9fa;
    border: 1.5px solid #e4e6ef;
    color: #5e6278;
    padding: 10px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-reset-filter:hover {
    background: #e4e6ef;
    color: #3f4254;
}

/* Modern Table Design */
.modern-table-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.modern-table-card .table {
    margin-bottom: 0;
}

.modern-table-card .table thead th {
    background: #f8f9fa;
    color: #5e6278;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 15px;
    border: none;
    border-bottom: 2px solid #e9ecef;
}

.modern-table-card .table tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid #f4f4f4;
    color: #3f4254;
    font-size: 14px;
}

.modern-table-card .table tbody tr:hover {
    background: #f8f9fa;
}

/* Modern Badge Design */
.badge-modern {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.badge-modern.badge-purple {
    background: #e8eaf6;
    color: #5e35b1;
}

.badge-modern.badge-green {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-modern.badge-orange {
    background: #fff3e0;
    color: #f57c00;
}

.badge-modern.badge-blue {
    background: #e3f2fd;
    color: #1976d2;
}

.badge-modern.badge-red {
    background: #ffebee;
    color: #c62828;
}

.badge-modern.badge-cyan {
    background: #e0f7fa;
    color: #00838f;
}

.badge-modern.badge-grey {
    background: #eceff1;
    color: #546e7a;
}

/* Modern Pagination */
.modern-pagination .pagination {
    margin-bottom: 0;
}

.modern-pagination .page-link {
    border-radius: 6px;
    margin: 0 3px;
    border: 1px solid #e4e6ef;
    color: #5e6278;
    padding: 8px 12px;
    font-weight: 500;
}

.modern-pagination .page-link:hover {
    background: #667eea;
    border-color: #667eea;
    color: #fff;
}

.modern-pagination .page-item.active .page-link {
    background: #667eea;
    border-color: #667eea;
}

/* Employee Avatar in table */
.employee-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
    margin-right: 10px;
    color: #fff;
}

.employee-info {
    display: flex;
    align-items: center;
}

.employee-details {
    display: flex;
    flex-direction: column;
}

.employee-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.employee-id {
    font-size: 12px;
    color: #7f8c8d;
}

/* Status Dropdown Modern */
.status-dropdown-modern .dropdown-toggle {
    background: transparent;
    border: none;
    padding: 0;
    color: #667eea;
    font-size: 18px;
}

.status-dropdown-modern .dropdown-toggle:after {
    display: none;
}

.status-dropdown-modern .dropdown-menu {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #f0f0f0;
    padding: 8px;
}

.status-dropdown-modern .dropdown-item {
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    transition: all 0.2s;
}

.status-dropdown-modern .dropdown-item:hover {
    background: #f8f9fa;
    color: #667eea;
}

.status-dropdown-modern .dropdown-item i {
    margin-right: 8px;
    width: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-content h3 {
        font-size: 24px;
    }
    
    .filter-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-filter,
    .btn-reset-filter {
        width: 100%;
    }
}
</style>
