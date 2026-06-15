@extends('super_admin.layouts.app')

@section('content')

<style>
    :root {
        --primary-orange: #ff6b35;
        --secondary-orange: #f7931e;
        --navy-dark: #1a2332;
        --navy-light: #2c3e50;
        
        --orange-gradient: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
        --navy-gradient: linear-gradient(135deg, #1a2332 0%, #2c3e50 100%);
        --green-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        --blue-gradient: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        
        --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        --card-shadow-hover: 0 15px 35px rgba(0, 0, 0, 0.12);
        --glow-orange: 0 0 20px rgba(255, 107, 53, 0.2);
        
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dashboard-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #dee2e6 100%);
        min-height: 100vh;
        padding: 1.5rem 0;
        position: relative;
    }

    .dashboard-container::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(255, 107, 53, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(26, 35, 50, 0.05) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    .dashboard-hero {
        background: var(--orange-gradient);
        border-radius: var(--border-radius);
        padding: 2.5rem;
        margin: 1.5rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow), var(--glow-orange);
        z-index: 1;
        animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 2;
    }

    .hero-subtitle {
        font-size: 1rem;
        opacity: 0.95;
        margin: 0.5rem 0 0 0;
        position: relative;
        z-index: 2;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.8rem;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        border-top: 4px solid var(--primary-orange);
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-shadow-hover);
        border-top-color: var(--secondary-orange);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 15px;
        right: 15px;
        width: 70px;
        height: 70px;
        background: var(--orange-gradient);
        border-radius: 50%;
        opacity: 0.1;
        transition: var(--transition);
    }

    .stat-card:hover::before {
        transform: scale(1.2);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(247, 147, 30, 0.1));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1rem;
        color: var(--primary-orange);
        position: relative;
        z-index: 2;
    }

    .stat-content h3 {
        font-size: 13px;
        color: #6c757d;
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        z-index: 2;
    }

    .stat-number {
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--navy-dark);
        margin: 0.5rem 0 0 0;
        position: relative;
        z-index: 2;
        background: linear-gradient(135deg, var(--navy-dark) 0%, var(--primary-orange) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-description {
        font-size: 0.85rem;
        color: #999;
        margin-top: 0.5rem;
        position: relative;
        z-index: 2;
    }

    .admin-status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .badge-active {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .badge-inactive {
        background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
        color: white;
    }

    .content {
        padding: 0;
        margin: 0;
    }

    .page-header {
        display: none;
    }

    .action-section {
        margin: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .btn-action {
        background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-orange) 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(255, 107, 53, 0.2);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action:hover {
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.3);
        transform: translateY(-2px);
        color: white;
    }

    @media (max-width: 768px) {
        .dashboard-hero {
            margin: 1rem;
            padding: 1.5rem;
        }

        .hero-title {
            font-size: 1.8rem;
        }

        .stats-grid {
            margin: 1rem;
            grid-template-columns: 1fr;
        }

        .stat-card {
            padding: 1.2rem;
        }

        .action-section {
            margin: 1rem;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="content container-fluid">

    <!-- Dashboard Hero Section -->
    <div class="dashboard-hero">
        <h1 class="hero-title">Welcome to Super Admin Dashboard</h1>
        <p class="hero-subtitle">Monitor system performance and manage administrators</p>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        
        <!-- Total Admins Card -->
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Total Admins</h3>
                <div class="stat-number">{{ $totalAdmins }}</div>
                <div class="stat-description">Total administrator accounts</div>
            </div>
        </div>

        <!-- Active Admins Card -->
        <div class="stat-card" style="border-top-color: #28a745;">
            <div class="stat-icon" style="color: #28a745; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3>Active Admins</h3>
                <div class="stat-number" style="color: #28a745;">{{ $totalAdmins > 0 ? $totalAdmins : 0 }}</div>
                <div class="stat-description">Currently active accounts</div>
            </div>
        </div>

        <!-- System Status Card -->
        <div class="stat-card" style="border-top-color: #007bff;">
            <div class="stat-icon" style="color: #007bff; background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 86, 179, 0.1));">
                <i class="fas fa-server"></i>
            </div>
            <div class="stat-content">
                <h3>System Status</h3>
                <div class="stat-number" style="color: #007bff;">
                    <span class="admin-status-badge badge-active">Active</span>
                </div>
                <div class="stat-description">All systems operational</div>
            </div>
        </div>

        <!-- Last Activity Card -->
        <div class="stat-card" style="border-top-color: #f7931e;">
            <div class="stat-icon" style="color: #f7931e; background: linear-gradient(135deg, rgba(247, 147, 30, 0.1), rgba(255, 107, 53, 0.1));">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>Current Time</h3>
                <div class="stat-number" style="color: #f7931e; font-size: 1.8rem;" id="currentTime">
                    {{ date('H:i') }}
                </div>
                <div class="stat-description">{{ date('l, F d, Y') }}</div>
            </div>
        </div>

    </div>

    <!-- Action Section -->
    <div class="action-section">
        <a href="{{ route('superadmin.admins.index') }}" class="btn-action">
            <i class="fas fa-cog me-2"></i> Manage Admins
        </a>
        <a href="{{ route('superadmin.admins.create') }}" class="btn-action" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);">
            <i class="fas fa-plus me-2"></i> Create Admin
        </a>
    </div>

</div>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('currentTime').textContent = hours + ':' + minutes;
    }

    // Update time every minute
    setInterval(updateTime, 60000);

    // Add subtle parallax effect
    window.addEventListener('scroll', function() {
        const hero = document.querySelector('.dashboard-hero');
        if (hero) {
            hero.style.transform = 'translateY(' + (window.scrollY * 0.5) + 'px)';
        }
    });

    // Animate stat cards on scroll into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.5s ease forwards';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.stat-card').forEach(card => {
        observer.observe(card);
    });
</script>

@endsection
