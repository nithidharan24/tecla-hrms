{{-- Super Admin Sidebar --}}

<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">Main</li>
                
                <li>
                    <a href="{{ route('superadmin.dashboard') }}" @if(request()->route()->getName() == 'superadmin.dashboard') class="active" @endif>
                        <i class="fas fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('superadmin.admins.index') }}" @if(in_array(request()->route()->getName(), ['superadmin.admins.index', 'superadmin.admins.create', 'superadmin.admins.edit'])) class="active" @endif>
                        <i class="fas fa-users-cog"></i> <span>Manage Admins</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>

<!-- Mobile Overlay -->
<div id="mobile_overlay" class="mobile_overlay"></div>

<style>
/* ----------------------------------------
   FIXED ZOHO STYLE + SMALLER SIZE
---------------------------------------- */

.sidebar {
    width: 78px !important;              /* REDUCED WIDTH */
    background: #131c2e !important;
    margin-left: 0 !important;
    border-right: 1px solid #00000020;
}

.page-wrapper,
.page-content,
.page-container {
    margin-left: 78px !important;        /* MATCH NEW WIDTH */
    width: calc(100% - 78px) !important;
}

.sidebar-inner {
    padding-top: 15px;                   /* Smaller spacing */
}

/* ----------------------------------------
   ICON + TEXT ALIGN (COMPACT)
---------------------------------------- */
.sidebar-menu ul {
    padding: 0;
    margin: 0;
}

.sidebar-menu ul li {
    list-style: none;
    text-align: center;
    margin-bottom: 5px;                 /* Reduced spacing */
}

.sidebar-menu ul li a {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    padding: 8px 5px;                    /* Smaller padding */
    text-decoration: none;
    border-radius: 10px;                 /* Slightly smaller radius */
    transition: 0.2s all;
}

/* ICON */
.sidebar-menu a i {
    font-size: 18px !important;          /* Smaller icon */
    margin-bottom: 3px;
    color: #f97316  !important;
}

/* LABEL */
.sidebar-menu a span {
    font-size: 11px;                     /* Slightly smaller text */
    color: #e1e7f5;
    margin-top: 2px;
    letter-spacing: 0.15px;
}

/* ACTIVE + HOVER */
.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: #1f2b4d !important;
    transition: 0.15s;
}

/* Hide all section titles */
.menu-title {
    display: none !important;
}

/* -------------------------------------------------
   MOBILE SIDEBAR MODE (screen < 768px)
------------------------------------------------- */

@media (max-width: 768px) {

/* Sidebar hidden by default */
.sidebar {
    position: fixed;
    left: -260px;                   /* fully hidden */
    width: 260px !important;        /* full mobile menu width */
    height: 100%;
    z-index: 9999;
    transition: left 0.3s ease;
}

/* When opened */
.mobile-open .sidebar {
    left: 0 !important;
}

/* Content should not shift */
.page-wrapper,
.page-content,
.page-container {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Overlay behind the menu */
#mobile_overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.4);
    z-index: 9998;
    display: none;
}

.mobile-open #mobile_overlay {
    display: block;
}
}

</style>

<script>
    const mobileBtn = document.getElementById("mobile_btn");
    const mobileOverlay = document.getElementById("mobile_overlay");

    mobileBtn.addEventListener("click", function() {
        document.body.classList.toggle("mobile-open");
    });

    mobileOverlay.addEventListener("click", function() {
        document.body.classList.remove("mobile-open");
    });
</script>
