@extends('layouts.index')

@section('content')
    <div class="container py-6">
        <!-- Hero Section with animated gradient -->
        <div class="privacy-hero text-center mb-8">
            <div class="d-flex justify-content-center mb-4">
                <div class="gradient-line" style="height: 4px; width: 120px; background: linear-gradient(90deg, #FF6D00 0%, #FF9E00 100%); border-radius: 2px;"></div>
            </div>
            <h1 class="display-5 fw-bold text-dark mb-3" style="letter-spacing: -0.5px;">Privacy Policy</h1>
            <div class="d-flex align-items-center justify-content-center">
                <span class="badge bg-orange-soft text-orange rounded-pill px-3 py-1 fs-6 fw-medium">Last updated: October 23, 2024</span>
            </div>
        </div>

        <!-- Floating card with glass morphism effect -->
        <div class="policy-card bg-white p-5 p-lg-6 rounded-4 shadow-lg position-relative overflow-hidden border border-1 border-light">
            <!-- Modern background elements -->
            <div class="position-absolute top-0 end-0" style="width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,109,0,0.03) 0%, rgba(255,255,255,0) 70%); transform: translate(30%, -30%); z-index: 0;"></div>
            <div class="position-absolute bottom-0 start-0" style="width: 250px; height: 250px; background: radial-gradient(circle, rgba(255,158,0,0.03) 0%, rgba(255,255,255,0) 70%); transform: translate(-30%, 30%); z-index: 0;"></div>
            
            <!-- Introductory content -->
            <div class="position-relative z-1">
                <p class="lead text-dark mb-5 fs-5" style="line-height: 1.7;">Your privacy is important to us. It is <span class="text-gradient fw-semibold">TECLA Media</span>'s policy to respect your privacy regarding any information we may collect from you across our website, <a href="https://tecla.in" class="text-gradient text-decoration-none hover-underline">https://tecla.in</a>, and other sites we own and operate.</p>
            </div>

            <!-- Modern accordion-style sections -->
            <div class="policy-sections">
                <!-- Section 1 -->
                <div class="policy-section mb-6 pb-4 border-bottom border-light">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">Information We Collect</h2>
                            <p class="text-muted mb-3">We collect personal information for better user experience and service. The information we may collect includes:</p>
                            <ul class="modern-list">
                                <li class="d-flex align-items-start mb-2">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Personal data: name, email address, phone number, billing and shipping addresses</span>
                                </li>
                                <li class="d-flex align-items-start mb-2">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Usage data: IP address, browser type and version, pages visited, time spent on pages</span>
                                </li>
                                <li class="d-flex align-items-start mb-2">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Cookies and tracking technologies to enhance site functionality</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="policy-section mb-6 pb-4 border-bottom border-light">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">How We Use the Information</h2>
                            <p class="text-muted mb-3">We use the collected data to:</p>
                            <div class="modern-grid">
                                <div class="grid-item bg-orange-soft rounded-3 p-3">
                                    <svg class="mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 17L9 11L13 15L21 7M3 17L7 21L13 15M3 17L9 11M21 7L15 13M21 7L15 13M15 13L13 15M9 11L13 15" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-0 fw-medium">Improve user experience and customize our website</p>
                                </div>
                                <div class="grid-item bg-orange-soft rounded-3 p-3">
                                    <svg class="mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18 8C19.6569 8 21 6.65685 21 5C21 3.34315 19.6569 2 18 2C16.3431 2 15 3.34315 15 5C15 6.65685 16.3431 8 18 8ZM6 14C7.65685 14 9 12.6569 9 11C9 9.34315 7.65685 8 6 8C4.34315 8 3 9.34315 3 11C3 12.6569 4.34315 14 6 14ZM18 22C19.6569 22 21 20.6569 21 19C21 17.3431 19.6569 16 18 16C16.3431 16 15 17.3431 15 19C15 20.6569 16.3431 22 18 22ZM6 14C7.65685 14 9 12.6569 9 11C9 9.34315 7.65685 8 6 8C4.34315 8 3 9.34315 3 11C3 12.6569 4.34315 14 6 14ZM9 11L15 5M9 11L15 19M15 5L18 8M15 19L18 16" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-0 fw-medium">Provide customer support and respond to queries</p>
                                </div>
                                <div class="grid-item bg-orange-soft rounded-3 p-3">
                                    <svg class="mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16 8V16M12 11V16M8 14V16M3 10H21M5 8H19C20.1046 8 21 8.89543 21 10V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V10C3 8.89543 3.89543 8 5 8Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-0 fw-medium">Process transactions and manage payments</p>
                                </div>
                                <div class="grid-item bg-orange-soft rounded-3 p-3">
                                    <svg class="mb-2" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mb-0 fw-medium">Send newsletters and promotional materials</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Continue with other sections following the same modern pattern -->

                <!-- Contact Section -->
                <div class="policy-section">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">9</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">Contact Us</h2>
                            <p class="text-muted mb-4">If you have any questions about this Privacy Policy or how your data is handled, please contact us:</p>
                            <div class="contact-cards">
                                <div class="contact-card bg-light rounded-3 p-3 d-flex align-items-center">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium">Email</p>
                                        <a href="mailto:support@tecla.in" class="text-dark text-decoration-none hover-text-orange">support@tecla.in</a>
                                    </div>
                                </div>
                                <div class="contact-card bg-light rounded-3 p-3 d-flex align-items-center">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium">Phone</p>
                                        <span class="text-dark">+91 123 456 7890</span>
                                    </div>
                                </div>
                                <div class="contact-card bg-light rounded-3 p-3 d-flex align-items-center">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 21C15 17 19 14.4183 19 10C19 6.13401 15.866 3 12 3C8.13401 3 5 6.13401 5 10C5 14.4183 9 17 12 21Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium">Address</p>
                                        <span class="text-dark">TECLA Media Pvt. Ltd., 123 Main Street, Chennai, India</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern color scheme */
        :root {
            --orange: #FF6D00;
            --orange-light: #FF9E00;
            --orange-soft: rgba(255, 109, 0, 0.1);
        }
        
        .text-orange {
            color: var(--orange);
        }
        
        .bg-orange-soft {
            background-color: var(--orange-soft);
        }
        
        .text-gradient {
            background: linear-gradient(90deg, var(--orange), var(--orange-light));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline;
        }
        
        /* Modern list styling */
        .modern-list li {
            transition: all 0.3s ease;
            padding: 4px 0;
        }
        
        .modern-list li:hover {
            transform: translateX(4px);
        }
        
        /* Grid layout for features */
        .modern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }
        
        .grid-item {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 109, 0, 0.1);
        }
        
        .grid-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(255, 109, 0, 0.1);
        }
        
        /* Contact cards */
        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }
        
        .contact-card {
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
        }
        
        .contact-icon {
            transition: all 0.3s ease;
        }
        
        .contact-card:hover .contact-icon {
            background-color: rgba(255, 109, 0, 0.2) !important;
        }
        
        /* Hover effects */
        .hover-text-orange:hover {
            color: var(--orange) !important;
        }
        
        .hover-underline:hover {
            text-decoration: underline !important;
        }
        
        /* Section number animation */
        .section-number {
            transition: all 0.3s ease;
        }
        
        .policy-section:hover .section-number {
            background-color: var(--orange) !important;
            color: white !important;
            transform: scale(1.1);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modern-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Animation on scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.policy-section');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = "1";
                        entry.target.style.transform = "translateY(0)";
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            sections.forEach(section => {
                section.style.opacity = "0";
                section.style.transform = "translateY(20px)";
                section.style.transition = "all 0.6s ease";
                observer.observe(section);
            });
        });
    </script>
@endsection