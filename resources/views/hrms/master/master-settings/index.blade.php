@extends('layouts.index')

@section('content')

<style>
    .ms-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 25px;
    }

    .ms-title {
        font-size: 28px;
        font-weight: 700;
    }

    .ms-subtitle {
        font-size: 18px;
        color: #777;
    }

    .ms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
        margin-top: 20px;
    }

    .ms-card {
        background: #fff;
        border-radius: 14px;
        padding: 25px 20px;
        text-align: center;
        border: 1px solid #F97613;
        transition: 0.3s;
        cursor: pointer;
        text-decoration: none;
        color: #333;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);

    }

    .ms-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .ms-icon img {
        width: 55px;
        height: 55px;
        margin-bottom: 12px;
    }

    .ms-text {
        font-size: 17px;
        font-weight: 600;
    }
</style>

<div class="content container-fluid">

    <!-- PAGE TITLE -->
    <div class="ms-header">
        <div>
            <div class="ms-title">Master</div>
            <div class="ms-subtitle">Settings</div>
        </div>
    </div>

    <!-- MASTER CARDS -->
    <div class="ms-grid">

        <a href="{{ route('branches.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/branch.png"></div>
            <div class="ms-text">Branch</div>
        </a>

        <a href="{{ route('adminaccess.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/admin.png"></div>
            <div class="ms-text">Admin Access</div>
        </a>

        <a href="{{ route('services.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/service.png"></div>
            <div class="ms-text">Services</div>
        </a>

        <a href="{{ route('department.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/departments.png"></div>
            <div class="ms-text">Departments</div>
        </a>

        <a href="{{ route('designation.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/designations.png"></div>
            <div class="ms-text">Designations</div>
        </a>

        <a href="{{ route('hierarchy.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/hierarchy.png"></div>
            <div class="ms-text">Hierarchy</div>
        </a>

    </div>

    <!-- SETTINGS CARDS -->
    <div class="ms-header" style="margin-top:40px;">
        <div>
            <div class="ms-title">Settings</div>
            <div class="ms-subtitle">Configuration</div>
        </div>
    </div>

    <div class="ms-grid">

        <a href="{{ route('leave-settings.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/calendar.png"></div>
            <div class="ms-text">Leave Settings</div>
        </a>

        <a href="{{ route('holidays.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/holidays.png"></div>
            <div class="ms-text">Holidays</div>
        </a>

        <a href="{{ route('fontmaster.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/fonts.png"></div>
            <div class="ms-text">Fonts</div>
        </a>

        <a href="{{ route('settings.index') }}" class="ms-card">
            <div class="ms-icon"><img src="storage/settings.png"></div>
            <div class="ms-text">General Settings</div>
        </a>

    </div>

</div>

@endsection
