@extends('layouts.index')

@section('content')
    <div class="container py-5">
        <!-- Hero Section -->
        <div class="tos-hero text-center mb-6">
            <div class="d-flex justify-content-center mb-4">
                <div class="gradient-line" style="height: 4px; width: 100px; background: linear-gradient(90deg, #FF6D00 0%, #FF9E00 100%); border-radius: 2px;"></div>
            </div>
            <h1 class="display-5 fw-bold text-dark mb-3" style="letter-spacing: -0.5px;">Terms of Service</h1>
            <div class="d-flex align-items-center justify-content-center">
                <span class="badge bg-orange-soft text-orange rounded-pill px-3 py-1 fs-6 fw-medium">Last updated: October 23, 2024</span>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="tos-card bg-white p-5 p-lg-6 rounded-4 shadow-sm position-relative overflow-hidden border border-1 border-light">
            <!-- Decorative elements -->
            <div class="position-absolute top-0 end-0" style="width: 250px; height: 250px; background: radial-gradient(circle, rgba(255,109,0,0.03) 0%, rgba(255,255,255,0) 70%); transform: translate(30%, -30%); z-index: 0;"></div>
            <div class="position-absolute bottom-0 start-0" style="width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,158,0,0.03) 0%, rgba(255,255,255,0) 70%); transform: translate(-30%, 30%); z-index: 0;"></div>
            
            <!-- Introduction -->
            <div class="position-relative z-1">
                <p class="lead text-dark mb-5" style="line-height: 1.7;">Welcome to <span class="text-gradient fw-semibold">TECLA Media</span>. By accessing or using our website, <a href="https://tecla.in" class="text-gradient text-decoration-none hover-underline">https://tecla.in</a>, and any associated services, you agree to comply with and be bound by the following Terms of Service.</p>
            </div>

            <!-- Terms Sections -->
            <div class="tos-sections">
                <!-- Section 1 -->
                <div class="tos-section mb-6 pb-4 border-bottom border-light">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">Acceptance of Terms</h2>
                            <p class="text-muted mb-0">By accessing our services, you agree to these terms. If you do not agree, you may not access or use our services.</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="tos-section mb-6 pb-4 border-bottom border-light">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">Services Provided</h2>
                            <p class="text-muted mb-3"><strong>TECLA Media</strong> provides a wide range of services, including but not limited to:</p>
                            <div class="services-grid">
                                <div class="service-item bg-orange-soft rounded-3 p-3 d-flex align-items-center">
                                    <div class="service-icon bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13 21H21V13M13 3H21V11M3 21H11V13M3 11H11V3" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mb-0 fw-medium">E-Commerce Development</p>
                                </div>
                                <div class="service-item bg-orange-soft rounded-3 p-3 d-flex align-items-center">
                                    <div class="service-icon bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 20L15 4M9 12L15 12M17 20L21 20M3 4L7 4" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mb-0 fw-medium">Digital Marketing</p>
                                </div>
                                <div class="service-item bg-orange-soft rounded-3 p-3 d-flex align-items-center">
                                    <div class="service-icon bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 18H12.01M7 2H17C18.1046 2 19 2.89543 19 4V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V4C5 2.89543 5.89543 2 7 2Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mb-0 fw-medium">Web Development</p>
                                </div>
                                <div class="service-item bg-orange-soft rounded-3 p-3 d-flex align-items-center">
                                    <div class="service-icon bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 18H12.01M7 21H17C18.1046 21 19 20.1046 19 19V5C19 3.89543 18.1046 3 17 3H7C5.89543 3 5 3.89543 5 5V19C5 20.1046 5.89543 21 7 21Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mb-0 fw-medium">Mobile App Development</p>
                                </div>
                                <div class="service-item bg-orange-soft rounded-3 p-3 d-flex align-items-center">
                                    <div class="service-icon bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 3V5M15 3V5M9 19V21M15 19V21M5 9H3M5 15H3M21 9H19M21 15H19M7 19H17C18.1046 19 19 18.1046 19 17V7C19 5.89543 18.1046 5 17 5H7C5.89543 5 5 5.89543 5 7V17C5 18.1046 5.89543 19 7 19Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <p class="mb-0 fw-medium">Software Development</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="tos-section mb-6 pb-4 border-bottom border-light">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">User Responsibilities</h2>
                            <p class="text-muted mb-3">As a user, you agree to use our services responsibly and abide by the following:</p>
                            <ul class="modern-list">
                                <li class="d-flex align-items-start mb-3">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Provide accurate and truthful information when using our services.</span>
                                </li>
                                <li class="d-flex align-items-start mb-3">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Use our services for lawful purposes only and refrain from engaging in harmful activities.</span>
                                </li>
                                <li class="d-flex align-items-start mb-3">
                                    <svg class="flex-shrink-0 me-2 mt-1" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Respect the intellectual property rights of TECLA Media and third parties.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Continue with other sections following the same pattern -->

                <!-- Contact Section -->
                <div class="tos-section">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1">
                            <div class="section-number bg-orange-soft text-orange rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">8</div>
                        </div>
                        <div>
                            <h2 class="h4 fw-bold text-dark mb-3">Contact Us</h2>
                            <p class="text-muted mb-4">If you have any questions regarding these Terms of Service, please contact us at:</p>
                            <div class="contact-methods">
                                <div class="contact-method d-flex align-items-center mb-3">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 8L10.8906 13.2604C11.5624 13.7083 12.4376 13.7083 13.1094 13.2604L21 8M5 19H19C20.1046 19 21 18.1046 21 17V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V17C3 18.1046 3.89543 19 5 19Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <a href="mailto:support@tecla.in" class="text-dark text-decoration-none hover-text-orange fw-medium">support@tecla.in</a>
                                    </div>
                                </div>
                                <div class="contact-method d-flex align-items-center mb-3">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 5C3 3.89543 3.89543 3 5 3H8.27924C8.70967 3 9.09181 3.27543 9.22792 3.68377L10.7257 8.17721C10.8831 8.64932 10.6694 9.16531 10.2243 9.38787L7.96701 10.5165C9.06925 12.9612 11.0388 14.9308 13.4835 16.033L14.6121 13.7757C14.8347 13.3306 15.3507 13.1169 15.8228 13.2743L20.3162 14.7721C20.7246 14.9082 21 15.2903 21 15.7208V19C21 20.1046 20.1046 21 19 21H18C9.71573 21 3 14.2843 3 6V5Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-dark fw-medium">+91 123 456 7890</span>
                                    </div>
                                </div>
                                <div class="contact-method d-flex align-items-center">
                                    <div class="contact-icon bg-orange-soft rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M12 21C15 17 19 14.4183 19 10C19 6.13401 15.866 3 12 3C8.13401 3 5 6.13401 5 10C5 14.4183 9 17 12 21Z" stroke="#FF6D00" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-dark fw-medium">TECLA Media Pvt. Ltd., 123 Main Street, Chennai, India</span>
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
        
        /* Services grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .service-item {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 109, 0, 0.1);
        }
        
        .service-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(255, 109, 0, 0.1);
            background-color: rgba(255, 109, 0, 0.15) !important;
        }
        
        .service-icon {
            transition: all 0.3s ease;
        }
        
        .service-item:hover .service-icon {
            background-color: var(--orange) !important;
        }
        
        .service-item:hover .service-icon svg {
            stroke: white;
        }
        
        /* Contact methods */
        .contact-method {
            transition: all 0.3s ease;
        }
        
        .contact-method:hover .contact-icon {
            background-color: var(--orange) !important;
        }
        
        .contact-method:hover .contact-icon svg {
            stroke: white;
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
        
        .tos-section:hover .section-number {
            background-color: var(--orange) !important;
            color: white !important;
            transform: scale(1.1);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .tos-card {
                padding: 2rem 1.5rem !important;
            }
        }
    </style>

    <!-- Animation on scroll -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.tos-section');
            
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