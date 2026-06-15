{{-- header.blade.php --}}
@php
    $designationName = null;

    if (in_array(Session::get('role'), ['employee', 'hr', 'manager']) && Session::has('user_id')) {
        $designationName = DB::table('allemployees as e')
            ->join('designation as d', 'd.id', '=', 'e.designation') 
            ->where('e.id', Session::get('user_id'))                 
            ->value('d.designation');                                
    }
@endphp
@php
    $dashboardRoute = 'eemployee.dashboard'; // default fallback

    if (Session::get('role') === 'admin') {
        $dashboardRoute = 'admin.dashboard';
    }

    if (in_array(Session::get('role'), ['employee', 'hr'])) {
        $level = strtolower(trim(Session::get('hierarchy_level')));

        switch ($level) {
            case 'ceo':
            case 'founder':
            case 'director':
                $dashboardRoute = 'ceo.dashboard';
                break;

            case 'manager':
            case 'project manager':
            case 'delivery manager':
                $dashboardRoute = 'manager.dashboard';
                break;

            case 'team leader':
            case 'team lead':
            case 'lead developer':
                $dashboardRoute = 'teamlead.dashboard';
                break;

            case 'tester':
            case 'qa engineer':
            case 'qa lead':
                $dashboardRoute = 'tester.dashboard';
                break;

            case 'hr':
            case 'hr manager':
                $dashboardRoute = 'hr.dashboard';
                break;

            case 'employee':
            case 'software engineer':
            case 'junior developer':
            default:
                $dashboardRoute = 'eemployee.dashboard';
                break;
        }
    }
@endphp


<div class="header">
    
        <a href="javascript:void(0)" class="logo">
            <img src="{{asset('admin/assets/img/logo3.png')}}" width="100" height="100" alt="Logo" style="margin-left: 20px;margin-top: 10px;">
        </a>
        <a href="javascript:void(0)" class="logo2">
            <img src="{{asset('admin/assets/img/logo3.png')}}" width="100" height="100" alt="Logo" style="margin-left: 20px;margin-top: 10px;">
        </a>
    

    
    <a id="mobile_btn" class="mobile_btn text-dark" href="#sidebar"><i class="fa-solid fa-bars"></i></a>


    <ul class="nav user-menu">
{{-- Home & Dashboard Links --}}
<ul class="nav header-left-links d-none d-md-flex align-items-center">
    <li class="nav-item">
        @if(Session::get('role') !== 'admin')
        <a href="{{ url('/home') }}" class="nav-link header-link">
            <i class="fa-solid fa-house me-1"></i> Home
        </a
        @endif
    </li>

    <li class="nav-item">
        <a href="{{ route($dashboardRoute) }}" class="nav-link header-link">
            <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
        </a>
    </li>
    @if(Session::get('role') === 'admin')
<li class="nav-item d-none d-md-flex align-items-center me-2">
    <a href="{{ route('subscribecompany.index') }}" class="nav-link" title="Premium">
        <i class="fa-solid fa-crown" style="font-size:18px;color:#ff7a00;"></i>
    </a>
</li>
@endif

</ul>


      
    
       
    
       
       
        @if(Session::get('role') === 'admin')
        <li class="nav-item d-none d-md-flex align-items-center me-2">
            <a href="{{ route('master.settings') }}" class="nav-link" title="Settings">
                <i class="fa-solid fa-gear" style="font-size:18px;color:#ff7a00;"></i>
            </a>
        </li>
        @endif
        
        <li class="nav-item topbar-user dropdown">
            <a class="dropdown-toggle profile-pic d-flex align-items-center" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                <div class="profile-avatar">
                    @if(in_array(Session::get('role'), ['employee', 'hr', 'manager']) && Session::get('profile_image'))
                        <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                    @elseif(Session::get('role') === 'agent' && Session::get('profile_image'))
                        <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                    @else
                        <div class="profile-initials">
                            @if(Session::get('role') === 'admin')
                                A
                            @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                                {{ strtoupper(substr(Session::get('first_name'), 0, 1)) }}{{ strtoupper(substr(Session::get('last_name'), 0, 1)) }}
                            @elseif(Session::get('role') === 'agent')
                                {{ substr(Session::get('first_name'), 0, 1) }}{{ substr(Session::get('last_name'), 0, 1) }}
                            @else
                                G
                            @endif
                        </div>
                    @endif
                </div>
                <div class="profile-info d-none d-md-block">
                    <span class="profile-greeting">Hi,</span>
                   <span class="profile-name">
    @if(Session::get('role') === 'admin')
        @php
            $adminName = DB::table('admin_access')
                ->where('id', Session::get('admin_id'))
                ->value('name');
        @endphp
        {{ $adminName ?? 'Admin' }}
    @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
        {{ Session::get('first_name') }}
    @elseif(Session::get('role') === 'agent')
        {{ Session::get('first_name') }}
    @else
        Guest
    @endif
</span>

                </div>
                <i class="fa-solid fa-chevron-down profile-arrow"></i>
            </a>
            
            <ul class="dropdown-menu dropdown-user">
                <li class="dropdown-header">
                    <div class="user-profile-header">
                        <div class="profile-avatar-large">
                            @if(in_array(Session::get('role'), ['employee', 'hr', 'manager']) && Session::get('profile_image'))
                                <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                            @elseif(Session::get('role') === 'agent' && Session::get('profile_image'))
                                <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                            @else
                                <div class="profile-initials-large">
                                    @if(Session::get('role') === 'admin')
                                        A
                                    @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                                        {{ strtoupper(substr(Session::get('first_name'), 0, 1)) }}{{ strtoupper(substr(Session::get('last_name'), 0, 1)) }}
                                    @elseif(Session::get('role') === 'agent')
                                        {{ substr(Session::get('first_name'), 0, 1) }}{{ substr(Session::get('last_name'), 0, 1) }}
                                    @else
                                        G
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="user-details">
                            <h4 class="user-name">
                               @if(Session::get('role') === 'admin')
        @php
            $adminName = DB::table('admin_access')
                ->where('id', Session::get('admin_id'))
                ->value('name');
        @endphp
        {{ $adminName ?? 'Admin' }}
                                @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                                    {{ Session::get('first_name') }} {{ Session::get('last_name') }}
                                @elseif(Session::get('role') === 'agent')
                                    {{ Session::get('first_name') }} {{ Session::get('last_name') }}
                                @endif
                            </h4>
                            @if(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                                <p class="user-id">ID: {{ Session::get('employee_id') }}</p>
                            @endif
                            <p class="user-email">
                                @if(Session::get('role') === 'admin')
                                    {{ Session::get('email') }}
                                @elseif(in_array(Session::get('role'), ['employee', 'hr']))
                                    {{ Session::get('email') }}
                                @elseif(Session::get('role') === 'agent')
                                    {{ Session::get('email') }}
                                @endif
                            </p>
                            <span class="user-role-badge">
                                {{ $designationName ?? ucfirst(Session::get('role')) }}
                            </span>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>

                {{-- <CHANGE> Show "My Profile" and "Settings" only for non-admin.
                     Also wire My Profile to the employee details page using the logged-in employee's normal id from session. --}}
                @if(Session::get('role') !== 'admin')
                <li>
                    <a class="dropdown-item" href="{{ in_array(Session::get('role'), ['employee', 'hr', 'manager']) ? route('employee.show', Session::get('user_id')) : '#' }}">
                        <i class="fa-solid fa-user me-2"></i>
                        My Profile
                    </a>
                </li>
                <!-- <li>
                    <a class="dropdown-item" href="">
                        <i class="fa-solid fa-cog me-2"></i>
                        Settings
                    </a>
                </li> -->
                @endif

                <li>
                    <a class="dropdown-item" href="{{ route('reset-password') }}">
                        <i class="fa-solid fa-key me-2"></i>
                        Change Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                        <i class="fa-solid fa-sign-out-alt me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <div class="dropdown mobile-user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
            <div class="mobile-user-info">
                <div class="profile-avatar-mobile">
                    @if(in_array(Session::get('role'), ['employee', 'hr', 'manager']) && Session::get('profile_image'))
                        <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                    @elseif(Session::get('role') === 'agent' && Session::get('profile_image'))
                        <img src="{{ asset(Session::get('profile_image')) }}" alt="Profile Image" class="rounded-circle">
                    @else
                        <div class="profile-initials-mobile">
                            @if(Session::get('role') === 'admin')
                                A
                            @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                                {{ strtoupper(substr(Session::get('first_name'), 0, 1)) }}{{ strtoupper(substr(Session::get('last_name'), 0, 1)) }}
                            @elseif(Session::get('role') === 'agent')
                                {{ substr(Session::get('first_name'), 0, 1) }}{{ substr(Session::get('last_name'), 0, 1) }}
                            @else
                                G
                            @endif
                        </div>
                    @endif
                </div>
                <div class="mobile-user-details">
                    <span class="mobile-user-name">
                        @if(Session::get('role') === 'admin')
                            @php
                                $adminName = DB::table('admin_access')
                                    ->where('id', Session::get('admin_id'))
                                    ->value('name');
                            @endphp
                            {{ $adminName ?? 'Admin' }}
                        @elseif(in_array(Session::get('role'), ['employee', 'hr', 'manager']))
                            {{ Session::get('first_name') }} {{ Session::get('last_name') }}
                        @elseif(Session::get('role') === 'agent')
                            {{ Session::get('first_name') }} {{ Session::get('last_name') }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="dropdown-divider"></div>
    
            {{-- Mobile: Show "My Profile" and "Change Password" for non-admin users --}}
            @if(Session::get('role') !== 'admin')
            <a class="dropdown-item" href="{{ in_array(Session::get('role'), ['employee', 'hr']) ? route('employee.show', Session::get('user_id')) : '#' }}">
                <i class="fa-solid fa-user me-2"></i>My Profile
            </a>
            @endif
    
            {{-- Always show "Change Password" for all users --}}
            <a class="dropdown-item" href="{{ route('reset-password') }}">
                <i class="fa-solid fa-key me-2"></i>Change Password
            </a>
    
            <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                <i class="fa-solid fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
</div>

<style>
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

    .header {
        background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%) !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Community Link Styling */
    .community-link {
        display: flex !important;
        align-items: center;
        gap: 8px;
        padding: 8px 12px !important;
        border-radius: 6px;
        transition: all 0.3s ease;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .community-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white !important;
    }

    .nav-text {
        font-size: 14px;
        font-weight: 500;
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
/* Force the hamburger bars to be orange */
#toggle_btn .bar-icon span {
    background-color: #ff7a00 !important; /* bright orange */
}
.profile-arrow {
    color: #ff7a00 !important; /* bright orange */
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
        color: rgba(255, 255, 255, 0.7);
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

    .profile-avatar-large {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
    }

    .profile-avatar-large img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.3);
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

    .user-id {
        font-size: 12px;
        margin: 0 0 4px 0;
        color: rgba(255, 255, 255, 0.8);
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

    /* Mobile Menu Styles */
    .mobile-user-info {
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #e65011, #ff7f16);
        color: white;
        margin: -8px -8px 0 -8px;
        border-radius: 8px 8px 0 0;
    }

    .profile-avatar-mobile {
        width: 40px;
        height: 40px;
    }

    .profile-avatar-mobile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .profile-initials-mobile {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .mobile-user-name {
        font-size: 14px;
        font-weight: 600;
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-info {
            display: none;
        }
        
        .profile-avatar {
            margin-right: 0;
        }
    }

    /* Animation for dropdown */
    .dropdown-user {
        animation: fadeInUp 0.3s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    /* Mobile Logo Adjustment */
@media (max-width: 768px) {
   

    /* Optional: center header content */
    .header {
        justify-content: center !important;
        text-align: center !important;
    }
}

</style>
<script>
    function logout() {
        window.location.href = "{{ route('logout') }}";
    }
</script>