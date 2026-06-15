{{-- Super Admin Header --}}

<div class="header">
    
    <a href="javascript:void(0)" class="logo">
        <img src="{{asset('admin/assets/img/logo3.png')}}" width="100" height="100" alt="Logo" style="margin-left: 20px;margin-top: 10px;">
    </a>
    <a href="javascript:void(0)" class="logo2">
        <img src="{{asset('admin/assets/img/logo3.png')}}" width="100" height="100" alt="Logo" style="margin-left: 20px;margin-top: 10px;">
    </a>

    <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa-solid fa-bars"></i></a>

    <ul class="nav user-menu">
        {{-- Left Navigation Links --}}
        <ul class="nav header-left-links d-none d-md-flex align-items-center">
            <li class="nav-item">
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link header-link">
                    <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                </a>
            </li>
        </ul>
        
        <li class="nav-item topbar-user dropdown">
            <a class="dropdown-toggle profile-pic d-flex align-items-center" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                <div class="profile-avatar">
                    <div class="profile-initials">SA</div>
                </div>
                <div class="profile-info d-none d-md-block">
                    <span class="profile-greeting">Welcome</span>
                    <span class="profile-name">{{ Session::get('super_admin_name') ?? 'Super Admin' }}</span>
                </div>
                <i class="fa-solid fa-chevron-down profile-arrow"></i>
            </a>
            
            <ul class="dropdown-menu dropdown-user">
                <li class="dropdown-header">
                    <div class="user-profile-header">
                        <div class="profile-initials-large">SA</div>
                        <div class="user-details">
                            <h4 class="user-name">{{ Session::get('super_admin_name') ?? 'Super Admin' }}</h4>
                            <p class="user-email">{{ Session::get('super_admin_email') ?? Session::get('email') ?? 'admin@example.com' }}</p>
                            <span class="user-role-badge">Super Administrator</span>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('superadmin.dashboard') }}">
                        <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('superadmin.logout') }}">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>

</div>

<style>
    .header {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%) !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .header-left-links {
        margin-right: auto; /* Push rest of header to right */
        padding-left: 15px;
    }

    .header-link {
        color: #ff7a00 !important;
        font-weight: 600;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.25s ease;
    }

    .header-link:hover {
        background: rgba(255, 122, 0, 0.1);
        color: #e65011 !important;
    }

    /* Profile Avatar Styles */
    .profile-avatar {
        width: 36px;
        height: 36px;
        margin-right: 8px;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .profile-initials {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .profile-info {
        display: flex;
        flex-direction: column;
        margin-right: 8px;
    }

    .profile-greeting {
        font-size: 12px;
        color: rgba(255, 55, 0, 0.8);
        line-height: 1;
    }

    .profile-name {
        font-size: 14px;
        font-weight: 600;
        color: rgba(255, 55, 0, 0.8);
        line-height: 1.2;
    }

    .profile-arrow {
        font-size: 12px;
        color: #ff7a00 !important;
        transition: transform 0.3s ease;
    }

    .dropdown-toggle[aria-expanded="true"] .profile-arrow {
        transform: rotate(180deg);
    }

    /* Dropdown Menu Styles */
    .dropdown-user {
        min-width: 280px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        padding: 0;
        margin-top: 8px;
    }

    .dropdown-header {
        padding: 0;
        border: none;
        background: none;
    }

    .user-profile-header {
        padding: 20px;
        background: linear-gradient(135deg, #e65011, #ff7f16);
        border-radius: 12px 12px 0 0;
        color: white;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .profile-initials-large {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 24px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .user-details {
        flex: 1;
    }

    .user-name {
        font-size: 16px;
        font-weight: 600;
        margin: 0 0 4px 0;
        color: white;
    }

    .user-email {
        font-size: 13px;
        margin: 0 0 8px 0;
        color: rgba(255, 255, 255, 0.9);
    }

    .user-role-badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .dropdown-user .dropdown-item {
        padding: 12px 20px;
        font-size: 14px;
        color: #333;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .dropdown-user .dropdown-item:hover {
        background-color: #f8f9fa;
        color: #e65011;
    }

    .dropdown-user .dropdown-item.text-danger:hover {
        background-color: #fff5f5;
        color: #dc3545;
    }

    .dropdown-user .dropdown-item i {
        width: 16px;
        text-align: center;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .profile-info {
            display: none;
        }
        
        .profile-avatar {
            margin-right: 0;
        }
    }

    /* Animations */
    .dropdown-user {
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
