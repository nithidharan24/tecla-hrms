<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tecla Careers - Join Our Team</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Keep all your existing CSS styles here */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 50%, #553c9a 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(26, 54, 93, 0.15);
            border-bottom: 1px solid rgba(213, 158, 46, 0.1);
        }
        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(45deg, #1a365d, #d69e2e, #553c9a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #2d3748;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-links a:hover {
            color: #1a365d;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        /* Hero Section */
        .hero {
            padding: 120px 0 80px;
            text-align: center;
            color: white;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(26, 54, 93, 0.1), rgba(213, 158, 46, 0.05), rgba(85, 60, 154, 0.1));
            pointer-events: none;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInUp 1s ease;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            background: linear-gradient(45deg, #ffffff, #f7fafc, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 1s ease 0.2s both;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.4s both;
        }
        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
        }
        .btn-primary {
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            color: white;
            box-shadow: 0 8px 25px rgba(213, 158, 46, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(213, 158, 46, 0.5);
            background: linear-gradient(45deg, #b7791f, #975a16);
        }
        /* Main Content */
        .main-content {
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border-radius: 20px 20px 0 0;
            margin-top: -20px;
            position: relative;
            z-index: 10;
            min-height: 100vh;
            box-shadow: 0 -8px 40px rgba(26, 54, 93, 0.15);
        }
        .content-wrapper {
            padding: 60px 0;
        }
        /* Job Listings Section */
        .jobs-section {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .filters {
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.03), rgba(213, 158, 46, 0.03));
            padding: 2rem;
            border-radius: 15px;
            height: fit-content;
            position: sticky;
            top: 120px;
            border: 1px solid rgba(26, 54, 93, 0.1);
            backdrop-filter: blur(10px);
        }
        .filter-group {
            margin-bottom: 2rem;
        }
        .filter-group h3 {
            margin-bottom: 1rem;
            color: #1a365d;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-group h3 i {
            color: #d69e2e;
        }
        .filter-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(26, 54, 93, 0.15);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            color: #2d3748;
        }
        .filter-input:focus {
            outline: none;
            border-color: #1a365d;
            box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
        }
        /* Search input container */
        .search-container {
            position: relative;
            display: flex;
            gap: 8px;
        }
        .search-input-wrapper {
            flex: 1;
            position: relative;
        }
        .search-btn {
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 80px;
        }
        .search-btn:hover {
            background: linear-gradient(45deg, #b7791f, #975a16);
            transform: translateY(-1px);
        }
        .clear-btn {
            background: transparent;
            color: #4a5568;
            border: 2px solid rgba(26, 54, 93, 0.2);
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        .clear-btn:hover {
            background: rgba(26, 54, 93, 0.05);
            border-color: #1a365d;
            color: #1a365d;
        }
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .filter-tag {
            padding: 6px 12px;
            background: rgba(26, 54, 93, 0.08);
            border: 1px solid rgba(26, 54, 93, 0.15);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #4a5568;
        }
        .filter-tag:hover,
        .filter-tag.active {
            background: linear-gradient(45deg, #1a365d, #2c5282);
            color: white;
            border-color: #1a365d;
            transform: translateY(-1px);
        }
        .jobs-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .job-card {
            background: linear-gradient(135deg, #ffffff 0%, rgba(247, 250, 252, 0.5) 100%);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(26, 54, 93, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(26, 54, 93, 0.1);
            position: relative;
        }
        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            border-radius: 15px 15px 0 0;
        }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(26, 54, 93, 0.15);
            border-color: #d69e2e;
        }
        .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .job-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 0.5rem;
        }
        .job-company {
            color: #4a5568;
            font-size: 1rem;
            font-weight: 500;
        }
        .job-location {
            color: #718096;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.3rem;
        }
        .job-salary {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .job-meta {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }
        .job-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .job-meta-item i {
            color: #d69e2e;
        }
        .job-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        .skill-tag {
            padding: 4px 12px;
            background: linear-gradient(45deg, #1a365d, #2c5282);
            color: white;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .job-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn-apply {
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-apply:hover {
            background: linear-gradient(45deg, #b7791f, #975a16);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(213, 158, 46, 0.4);
        }
        .btn-save {
            background: transparent;
            color: #4a5568;
            padding: 10px 20px;
            border: 2px solid rgba(26, 54, 93, 0.2);
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            background: rgba(26, 54, 93, 0.05);
            border-color: #1a365d;
            color: #1a365d;
            transform: translateY(-2px);
        }
        /* Stats Section */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
            padding: 2rem 0;
        }
        .stat-card {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(26, 54, 93, 0.05), rgba(213, 158, 46, 0.05));
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(26, 54, 93, 0.1);
            position: relative;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            border-radius: 15px 15px 0 0;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(26, 54, 93, 0.15);
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #1a365d, #d69e2e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-label {
            font-size: 1.1rem;
            color: #4a5568;
            margin-top: 0.5rem;
            font-weight: 500;
        }
        /* Loading state */
        .loading {
            text-align: center;
            padding: 2rem;
            color: #4a5568;
        }
        .no-jobs {
            text-align: center;
            padding: 3rem;
            color: #4a5568;
        }
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            animation: slideInUp 0.3s ease;
            box-shadow: 0 20px 60px rgba(26, 54, 93, 0.3);
        }
        .modal-header {
            padding: 2rem 2rem 1rem;
            border-bottom: 1px solid rgba(26, 54, 93, 0.1);
            position: relative;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 0.5rem;
        }
        .modal-subtitle {
            color: #4a5568;
            font-size: 1rem;
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #4a5568;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .modal-close:hover {
            background: rgba(26, 54, 93, 0.1);
            color: #1a365d;
        }
        .modal-body {
            padding: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9rem;
        }
        .form-label.required::after {
            content: ' *';
            color: #e53e3e;
        }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(26, 54, 93, 0.15);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            color: #2d3748;
        }
        .form-input:focus {
            outline: none;
            border-color: #1a365d;
            box-shadow: 0 0 0 3px rgba(26, 54, 93, 0.1);
        }
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .file-upload-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 2rem;
            border: 2px dashed rgba(26, 54, 93, 0.3);
            border-radius: 8px;
            background: rgba(26, 54, 93, 0.02);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            color: #4a5568;
        }
        .file-upload-label:hover {
            border-color: #1a365d;
            background: rgba(26, 54, 93, 0.05);
        }
        .file-upload-label i {
            font-size: 2rem;
            color: #d69e2e;
        }
        .file-upload-text {
            font-weight: 500;
        }
        .file-upload-subtext {
            font-size: 0.8rem;
            color: #718096;
            margin-top: 0.25rem;
        }
        .file-selected {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(213, 158, 46, 0.1);
            border-radius: 5px;
            font-size: 0.9rem;
            color: #1a365d;
            display: none;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .modal-footer {
            padding: 1rem 2rem 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .btn-modal {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .btn-modal-primary {
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            color: white;
        }
        .btn-modal-primary:hover {
            background: linear-gradient(45deg, #b7791f, #975a16);
            transform: translateY(-1px);
        }
        .btn-modal-secondary {
            background: transparent;
            color: #4a5568;
            border: 2px solid rgba(26, 54, 93, 0.2);
        }
        .btn-modal-secondary:hover {
            background: rgba(26, 54, 93, 0.05);
            border-color: #1a365d;
            color: #1a365d;
        }
        .error-message {
            color: #e53e3e;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }
        .success-message {
            background: #c6f6d5;
            color: #22543d;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }
        /* Success state styles */
        .success-footer {
            padding: 1rem 2rem 2rem;
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn-close-success {
            background: linear-gradient(45deg, #d69e2e, #b7791f);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .btn-close-success:hover {
            background: linear-gradient(45deg, #b7791f, #975a16);
            transform: translateY(-1px);
        }
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .jobs-section {
                grid-template-columns: 1fr;
            }
            .filters {
                position: static;
            }
            .nav-links {
                display: none;
            }
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            .job-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .job-actions {
                flex-direction: column;
            }
            .search-container {
                flex-direction: column;
            }
            .search-btn, .clear-btn {
                width: 100%;
            }
            .modal {
                width: 95%;
                margin: 1rem;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
            .modal-footer {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav container">
            <div class="logo">tecla</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#jobs">Jobs</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Shape Your Future With Us</h1>
            <p>Discover amazing career opportunities and join our innovative team of professionals</p>
            <div class="cta-buttons">
                <a href="#jobs" class="btn btn-primary">Browse Jobs</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container content-wrapper">
            <!-- Stats Section -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $jobs->count() }}</div>
                    <div class="stat-label">Open Positions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $departments->count() }}</div>
                    <div class="stat-label">Departments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Support</div>
                </div>
            </div>

            <!-- Jobs Section -->
            <div id="jobs" class="jobs-section">
                <!-- Filters Sidebar -->
                <aside class="filters">
                    <div class="filter-group">
                        <h3><i class="fas fa-search"></i> Search</h3>
                        <div class="search-container">
                            <div class="search-input-wrapper">
                                <input type="text" id="search-input" class="filter-input" placeholder="Job title, keywords...">
                            </div>
                            <button type="button" id="search-btn" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3><i class="fas fa-map-marker-alt"></i> Location</h3>
                        <div class="search-container">
                            <div class="search-input-wrapper">
                                <input type="text" id="location-input" class="filter-input" placeholder="City, state, country...">
                            </div>
                            <button type="button" id="location-btn" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <button type="button" id="clear-all-btn" class="clear-btn" style="width: 100%; margin-bottom: 1rem;">
                            <i class="fas fa-times"></i> Clear All Filters
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <h3><i class="fas fa-building"></i> Department</h3>
                        <div class="filter-tags">
                            <span class="filter-tag active" data-filter="department" data-value="All">All</span>
                            @foreach($departments as $dept)
                                <span class="filter-tag" data-filter="department" data-value="{{ $dept->department }}">{{ $dept->department }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3><i class="fas fa-clock"></i> Job Type</h3>
                        <div class="filter-tags">
                            <span class="filter-tag active" data-filter="job_type" data-value="All">All</span>
                            @foreach($jobTypes as $type)
                                <span class="filter-tag" data-filter="job_type" data-value="{{ $type }}">{{ ucfirst($type) }}</span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="filter-group">
                        <h3><i class="fas fa-layer-group"></i> Experience</h3>
                        <div class="filter-tags">
                            <span class="filter-tag active" data-filter="experience" data-value="All">All</span>
                            @foreach($experienceLevels as $exp)
                                <span class="filter-tag" data-filter="experience" data-value="{{ $exp }}">{{ $exp }}</span>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <!-- Jobs List -->
                <div class="jobs-list" id="jobs-container">
                    @forelse($jobs as $job)
                        <div class="job-card" data-job-id="{{ $job->id }}">
                            <div class="job-header">
                                <div>
                                    <h3 class="job-title">{{ $job->job_title }}</h3>
                                    <p class="job-company">{{ $job->department }}</p>
                                    <p class="job-location">
                                        <i class="fas fa-map-marker-alt"></i> {{ $job->job_location }}
                                    </p>
                                </div>
                                <div class="job-salary">${{ number_format($job->salary_from) }}K - ${{ number_format($job->salary_to) }}K</div>
                            </div>
                            <div class="job-meta">
                                <span class="job-meta-item">
                                    <i class="fas fa-clock"></i> {{ ucfirst($job->job_type) }}
                                </span>
                                <span class="job-meta-item">
                                    <i class="fas fa-calendar"></i> Posted {{ \Carbon\Carbon::parse($job->created_at)->diffForHumans() }}
                                </span>
                                <span class="job-meta-item">
                                    <i class="fas fa-users"></i> {{ $job->vacancies }} {{ $job->vacancies > 1 ? 'positions' : 'position' }}
                                </span>
                            </div>
                            <div class="job-skills">
                                @if($job->skills)
                                    @foreach(explode(',', $job->skills) as $skill)
                                        <span class="skill-tag">{{ trim($skill) }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <div class="job-actions">
                                <button class="btn-apply" data-job-id="{{ $job->id }}" data-job-title="{{ $job->job_title }}" data-job-department="{{ $job->department }}">Apply Now</button>
                                <button class="btn-save" data-job-id="{{ $job->id }}">
                                    <i class="fas fa-heart"></i> Save
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="no-jobs">
                            <h3>No jobs available at the moment</h3>
                            <p>Please check back later for new opportunities.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- Job Application Modal -->
    <div class="modal-overlay" id="applicationModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Apply for Position</h2>
                <p class="modal-subtitle" id="modalJobTitle">Software Developer - Engineering</p>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-message" id="successMessage">
                    <i class="fas fa-check-circle"></i> Your application has been submitted successfully! We'll get back to you soon.
                </div>
                <form id="applicationForm" enctype="multipart/form-data">
                    <input type="hidden" id="jobId" name="job_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="firstName">First Name</label>
                            <input type="text" id="firstName" name="first_name" class="form-input" required>
                            <div class="error-message" id="firstNameError"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="last_name" class="form-input" required>
                            <div class="error-message" id="lastNameError"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" required>
                            <div class="error-message" id="emailError"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" required>
                            <div class="error-message" id="phoneError"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="linkedin">LinkedIn Profile (Optional)</label>
                        <input type="url" id="linkedin" name="linkedin" class="form-input" placeholder="https://linkedin.com/in/yourprofile">
                    </div>

                    <div class="form-group">
                        <label class="form-label required" for="resume">Resume/CV</label>
                        <div class="file-upload">
                            <input type="file" id="resume" name="resume" class="file-upload-input" accept=".pdf,.doc,.docx" required>
                            <label for="resume" class="file-upload-label">
                                <div>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <div class="file-upload-text">Click to upload your resume</div>
                                    <div class="file-upload-subtext">PDF, DOC, DOCX (Max 5MB)</div>
                                </div>
                            </label>
                            <div class="file-selected" id="resumeSelected"></div>
                        </div>
                        <div class="error-message" id="resumeError"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required" for="coverLetter">Cover Letter</label>
                        <textarea id="coverLetter" name="cover_letter" class="form-input form-textarea" rows="5" placeholder="Tell us why you're interested in this position and what makes you a great fit..." required></textarea>
                        <div class="error-message" id="coverLetterError"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="experience">Years of Experience</label>
                        <select id="experience" name="years_experience" class="form-input">
                            <option value="">Select experience level</option>
                            <option value="0-1">0-1 years</option>
                            <option value="2-3">2-3 years</option>
                            <option value="4-5">4-5 years</option>
                            <option value="6-10">6-10 years</option>
                            <option value="10+">10+ years</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="salary">Expected Salary (Optional)</label>
                        <input type="text" id="salary" name="expected_salary" class="form-input" placeholder="e.g., $80,000 - $100,000">
                    </div>
                </form>
            </div>
            <!-- Default Footer (shown during form filling) -->
            <div class="modal-footer" id="defaultFooter">
                <button type="button" class="btn-modal btn-modal-secondary" id="cancelApplication">Cancel</button>
                <button type="submit" form="applicationForm" class="btn-modal btn-modal-primary" id="submitApplication">
                    <span class="submit-text">Submit Application</span>
                    <span class="submit-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i> Submitting...
                    </span>
                </button>
            </div>
            <!-- Success Footer (shown after successful submission) -->
            <div class="success-footer" id="successFooter" style="display: none;">
                <button type="button" class="btn-close-success" id="closeSuccessModal">
                    <i class="fas fa-check"></i> Close
                </button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Set up CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Filter functionality
            let currentFilters = {
                search: '',
                location: '',
                department: 'All',
                job_type: 'All',
                experience: 'All'
            };

            // Search button click handler
            $('#search-btn').on('click', function() {
                currentFilters.search = $('#search-input').val().trim();
                console.log('Search button clicked:', currentFilters.search);
                filterJobs();
            });

            // Location button click handler
            $('#location-btn').on('click', function() {
                currentFilters.location = $('#location-input').val().trim();
                console.log('Location button clicked:', currentFilters.location);
                filterJobs();
            });

            // Enter key handler for search inputs
            $('#search-input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    currentFilters.search = $(this).val().trim();
                    console.log('Search enter pressed:', currentFilters.search);
                    filterJobs();
                }
            });

            $('#location-input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    currentFilters.location = $(this).val().trim();
                    console.log('Location enter pressed:', currentFilters.location);
                    filterJobs();
                }
            });

            // Clear all filters button
            $('#clear-all-btn').on('click', function() {
                // Reset all filters
                currentFilters = {
                    search: '',
                    location: '',
                    department: 'All',
                    job_type: 'All',
                    experience: 'All'
                };
                
                // Clear input fields
                $('#search-input').val('');
                $('#location-input').val('');
                
                // Reset filter tags
                $('.filter-tag').removeClass('active');
                $('.filter-tag[data-value="All"]').addClass('active');
                
                console.log('All filters cleared');
                filterJobs();
            });

            // Filter tag handlers
            $(document).on('click', '.filter-tag', function() {
                const $parent = $(this).parent();
                const filterType = $(this).data('filter');
                const filterValue = $(this).data('value');

                // Remove active class from siblings
                $parent.find('.filter-tag').removeClass('active');
                $(this).addClass('active');

                // Update current filters
                currentFilters[filterType] = filterValue;
                console.log('Filter updated:', filterType, '=', filterValue);
                filterJobs();
            });

            // Filter jobs function
            function filterJobs() {
                const $jobsContainer = $('#jobs-container');
                $jobsContainer.html('<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading jobs...</div>');
                console.log('Sending filters:', currentFilters);

                // Make AJAX request to filter jobs
                $.ajax({
                    url: '{{ route("career.filter") }}',
                    method: 'POST',
                    data: currentFilters,
                    success: function(jobs) {
                        console.log('Received jobs:', jobs);
                        displayJobs(jobs);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseText);
                        $jobsContainer.html('<div class="no-jobs"><h3>Error loading jobs</h3><p>Please try again later.</p></div>');
                    }
                });
            }

            // Display jobs function
            function displayJobs(jobs) {
                const $jobsContainer = $('#jobs-container');
                
                if (jobs.length === 0) {
                    $jobsContainer.html('<div class="no-jobs"><h3>No jobs found</h3><p>Try adjusting your search criteria.</p></div>');
                    return;
                }

                let jobsHTML = '';
                jobs.forEach(function(job) {
                    const skills = job.skills ? job.skills.split(',').map(skill => 
                        `<span class="skill-tag">${skill.trim()}</span>`
                    ).join('') : '';
                    
                    const createdDate = new Date(job.created_at);
                    const now = new Date();
                    const diffTime = Math.abs(now - createdDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    const timeAgo = diffDays === 1 ? '1 day ago' : `${diffDays} days ago`;

                    jobsHTML += `
                        <div class="job-card" data-job-id="${job.id}">
                            <div class="job-header">
                                <div>
                                    <h3 class="job-title">${job.job_title}</h3>
                                    <p class="job-company">${job.department}</p>
                                    <p class="job-location">
                                        <i class="fas fa-map-marker-alt"></i> ${job.job_location}
                                    </p>
                                </div>
                                <div class="job-salary">$${Math.floor(job.salary_from/1000)}K - $${Math.floor(job.salary_to/1000)}K</div>
                            </div>
                            <div class="job-meta">
                                <span class="job-meta-item">
                                    <i class="fas fa-clock"></i> ${job.job_type.charAt(0).toUpperCase() + job.job_type.slice(1)}
                                </span>
                                <span class="job-meta-item">
                                    <i class="fas fa-calendar"></i> Posted ${timeAgo}
                                </span>
                                <span class="job-meta-item">
                                    <i class="fas fa-users"></i> ${job.vacancies} ${job.vacancies > 1 ? 'positions' : 'position'}
                                </span>
                            </div>
                            <div class="job-skills">
                                ${skills}
                            </div>
                            <div class="job-actions">
                                <button class="btn-apply" data-job-id="${job.id}" data-job-title="${job.job_title}" data-job-department="${job.department}">Apply Now</button>
                                <button class="btn-save" data-job-id="${job.id}">
                                    <i class="fas fa-heart"></i> Save
                                </button>
                            </div>
                        </div>
                    `;
                });

                $jobsContainer.html(jobsHTML);
            }

            // Modal functionality - UPDATED VERSION
            $(document).on('click', '.btn-apply', function() {
                const jobId = $(this).data('job-id');
                const jobTitle = $(this).data('job-title');
                const jobDepartment = $(this).data('job-department');
                
                // IMPORTANT: Reset form completely before setting new job info
                resetApplicationForm();
                
                // Set job information in modal
                $('#jobId').val(jobId);
                $('#modalJobTitle').text(`${jobTitle} - ${jobDepartment}`);
                
                // Show modal
                $('#applicationModal').addClass('show');
                $('body').css('overflow', 'hidden');
            });

            // Close modal handlers
            $('#closeModal, #cancelApplication').on('click', function() {
                closeModal();
            });

            // NEW: Close success modal handler
            $('#closeSuccessModal').on('click', function() {
                closeModal();
            });

            // Close modal when clicking overlay
            $('#applicationModal').on('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Close modal function
            function closeModal() {
                $('#applicationModal').removeClass('show');
                $('body').css('overflow', 'auto');
                resetApplicationForm();
            }

            // UPDATED: Reset form function
            function resetApplicationForm() {
                // Reset the form
                $('#applicationForm')[0].reset();
                
                // Hide all error messages
                $('.error-message').hide();
                
                // IMPORTANT: Hide success message and show form
                $('#successMessage').hide();
                $('#applicationForm').show();
                
                // UPDATED: Show default footer, hide success footer
                $('#defaultFooter').show();
                $('#successFooter').hide();
                
                // Reset file upload display
                $('#resumeSelected').hide();
                $('.file-upload-label').show();
                
                // Reset button states
                $('#submitApplication .submit-text').show();
                $('#submitApplication .submit-loading').hide();
                $('#submitApplication').prop('disabled', false);
                
                console.log('Form reset completed');
            }

            // File upload handler
            $('#resume').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit
                        $('#resumeError').text('File size must be less than 5MB').show();
                        this.value = '';
                        return;
                    }
                    
                    $('#resumeSelected').html(`
                        <i class="fas fa-file-alt"></i>
                        <strong>${fileName}</strong> (${fileSize} MB)
                        <button type="button" class="remove-file" style="margin-left: 10px; background: none; border: none; color: #e53e3e; cursor: pointer;">
                            <i class="fas fa-times"></i>
                        </button>
                    `).show();
                    $('#resumeError').hide();
                }
            });

            // Remove file handler
            $(document).on('click', '.remove-file', function() {
                $('#resume').val('');
                $('#resumeSelected').hide();
            });

            // UPDATED: Form submission
            $('#applicationForm').on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.error-message').hide();
                
                // Show loading state
                $('#submitApplication .submit-text').hide();
                $('#submitApplication .submit-loading').show();
                $('#submitApplication').prop('disabled', true);
                
                // Create FormData object
                const formData = new FormData(this);
                
                // Submit application
                $.ajax({
                    url: '{{ route("career.apply") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Hide form and show success message
                            $('#applicationForm').hide();
                            $('#successMessage').show();
                            
                            // UPDATED: Hide default footer and show success footer
                            $('#defaultFooter').hide();
                            $('#successFooter').show();
                            
                            // Auto close modal after 5 seconds (increased time)
                            setTimeout(function() {
                                closeModal();
                            }, 5000);
                        } else {
                            throw new Error(response.message || 'Application failed');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                const errorElement = $('#' + field.replace('_', '') + 'Error');
                                if (errorElement.length) {
                                    errorElement.text(errors[field][0]).show();
                                }
                            });
                        } else {
                            alert('An error occurred. Please try again.');
                        }
                    },
                    complete: function() {
                        // Reset loading state
                        $('#submitApplication .submit-text').show();
                        $('#submitApplication .submit-loading').hide();
                        $('#submitApplication').prop('disabled', false);
                    }
                });
            });

            // Save job handlers
            $(document).on('click', '.btn-save', function() {
                const $icon = $(this).find('i');
                if ($icon.hasClass('fas')) {
                    $icon.removeClass('fas').addClass('far');
                    $(this).css('color', '#4a5568');
                } else {
                    $icon.removeClass('far').addClass('fas');
                    $(this).css('color', '#1a365d');
                }
            });

            // Smooth scrolling for navigation links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                const target = $($(this).attr('href'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });

            // Animate stats on scroll
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeInUp 0.6s ease forwards';
                    }
                });
            });

            $('.stat-card').each(function() {
                observer.observe(this);
            });
        });
    </script>
</body>
</html>
