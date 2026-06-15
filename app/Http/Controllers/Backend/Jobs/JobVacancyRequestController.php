<?php

namespace App\Http\Controllers\Backend\Jobs;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
 
class JobVacancyRequestController extends Controller

{
    
    // <CHANGE> Added job titles and skills mapping for each department
    private $departmentJobTitles = [
        'Management' => [
            'Project Manager', 'Team Lead', 'Operations Manager', 'HR Manager', 'Finance Manager',
            'General Manager', 'Branch Manager', 'Assistant Manager', 'Department Head',
            'Business Manager', 'Program Manager', 'Portfolio Manager', 'Delivery Manager',
            'Product Manager', 'Account Manager', 'Regional Manager', 'Area Manager'
        ],
        'Web Development' => [
            'Frontend Developer', 'Backend Developer', 'Full Stack Developer', 'Web Developer',
            'React Developer', 'Angular Developer', 'Vue.js Developer', 'Node.js Developer',
            'PHP Developer', 'Laravel Developer', 'WordPress Developer', 'Drupal Developer',
            'Python Developer', 'Django Developer', 'Ruby on Rails Developer', 'ASP.NET Developer',
            'Java Developer', 'Spring Boot Developer', 'Web Development Lead', 'Senior Web Developer',
            'Junior Web Developer', 'Frontend Architect', 'Backend Architect', 'DevOps Engineer',
            'Web Performance Engineer', 'API Developer', 'Microservices Developer'
        ],
        'Testing' => [
            'QA Engineer', 'Test Automation Engineer', 'Manual Tester', 'QA Lead', 'Performance Tester',
            'Software Tester', 'QA Analyst', 'Test Engineer', 'Quality Analyst', 'Automation Tester',
            'Senior QA Engineer', 'Junior QA Engineer', 'QA Manager', 'Test Lead',
            'Security Tester', 'Penetration Tester', 'Load Tester', 'API Tester',
            'Mobile App Tester', 'Web Application Tester', 'Database Tester', 'ETL Tester',
            'UAT Coordinator', 'Test Analyst', 'SDET', 'Quality Assurance Specialist'
        ],
        'Designing' => [
            'UI Designer', 'UX Designer', 'Graphic Designer', 'Product Designer', 'Web Designer',
            'Visual Designer', 'Interaction Designer', 'UI/UX Designer', 'Motion Graphics Designer',
            'Brand Designer', 'Logo Designer', 'Illustration Designer', 'Creative Designer',
            'Digital Designer', '3D Designer', 'Game Designer', 'Art Director',
            'Senior Designer', 'Junior Designer', 'Design Lead', 'UX Researcher',
            'UX Writer', 'Design Manager', 'Print Designer', 'Packaging Designer'
        ],
        'App Development' => [
            'iOS Developer', 'Android Developer', 'Mobile Developer', 'Flutter Developer',
            'React Native Developer', 'Xamarin Developer', 'Ionic Developer', 'Kotlin Developer',
            'Swift Developer', 'Mobile App Architect', 'Senior Mobile Developer', 'Junior Mobile Developer',
            'Mobile UI Developer', 'Mobile Backend Developer', 'Hybrid App Developer',
            'Cross-Platform Developer', 'Mobile DevOps Engineer', 'Mobile QA Engineer',
            'Mobile Game Developer', 'AR/VR Developer', 'Wearable App Developer'
        ],
        'Marketing' => [
            'Marketing Manager', 'Digital Marketing Specialist', 'Content Marketing Manager',
            'SEO Specialist', 'Social Media Manager', 'Marketing Executive', 'Brand Manager',
            'Marketing Coordinator', 'Growth Marketer', 'Email Marketing Specialist',
            'Performance Marketing Manager', 'Content Writer', 'Copywriter', 'Marketing Analyst',
            'Product Marketing Manager', 'Campaign Manager', 'Influencer Marketing Manager',
            'Affiliate Marketing Manager', 'PPC Specialist', 'SEM Specialist',
            'Marketing Automation Specialist', 'Community Manager', 'Public Relations Manager',
            'Event Marketing Manager', 'Field Marketing Manager', 'Marketing Director'
        ],
        'Sales' => [
            'Sales Executive', 'Sales Manager', 'Business Development Executive', 'Account Manager',
            'Sales Representative', 'Sales Coordinator', 'Inside Sales Executive', 'Outside Sales Executive',
            'Sales Team Lead', 'Regional Sales Manager', 'Area Sales Manager', 'Territory Sales Manager',
            'Key Account Manager', 'Enterprise Sales Manager', 'B2B Sales Executive', 'B2C Sales Executive',
            'Sales Engineer', 'Pre-Sales Consultant', 'Sales Operations Manager', 'Sales Analyst',
            'Channel Sales Manager', 'Partner Manager', 'Business Development Manager',
            'Sales Director', 'VP Sales', 'Chief Sales Officer'
        ],
        'Support' => [
            'Customer Support Executive', 'Technical Support Engineer', 'Support Manager',
            'Help Desk Specialist', 'Support Lead', 'Customer Service Representative',
            'Technical Support Specialist', 'IT Support Specialist', 'Desktop Support Engineer',
            'Application Support Engineer', 'Customer Success Manager', 'Client Support Specialist',
            'Support Analyst', 'Service Desk Analyst', 'Senior Support Engineer',
            'Level 1 Support', 'Level 2 Support', 'Level 3 Support', 'Escalation Manager',
            'Support Operations Manager', 'Customer Care Executive', 'Chat Support Executive',
            'Email Support Specialist', 'Phone Support Specialist'
        ],
        'HR' => [
            'HR Executive', 'HR Manager', 'Recruiter', 'HR Coordinator', 'Talent Acquisition Specialist',
            'Senior HR Executive', 'HR Generalist', 'HR Business Partner', 'Recruitment Manager',
            'Talent Acquisition Manager', 'HR Operations Manager', 'Payroll Executive',
            'Compensation & Benefits Manager', 'Employee Relations Manager', 'Training Manager',
            'Learning & Development Manager', 'Organizational Development Manager',
            'HR Analytics Manager', 'HRIS Manager', 'Onboarding Specialist',
            'Performance Management Specialist', 'Workforce Planning Manager', 'HR Director',
            'VP Human Resources', 'Chief Human Resources Officer'
        ],
        'Accounts Admin' => [
            'Accountant', 'Accounts Manager', 'Finance Executive', 'Bookkeeper', 'Accounts Coordinator',
            'Senior Accountant', 'Junior Accountant', 'Accounts Payable Specialist',
            'Accounts Receivable Specialist', 'Financial Analyst', 'Cost Accountant',
            'Tax Accountant', 'Audit Associate', 'Internal Auditor', 'Financial Controller',
            'Finance Manager', 'Chief Financial Officer', 'Finance Director', 'Treasury Manager',
            'Credit Controller', 'Billing Executive', 'Payroll Accountant', 'Fixed Assets Accountant',
            'Management Accountant', 'Chartered Accountant'
        ],
        'IT Support' => [
            'IT Support Engineer', 'System Administrator', 'IT Support Manager', 'Network Administrator',
            'IT Technician', 'Desktop Support Engineer', 'Infrastructure Engineer', 'Server Administrator',
            'Database Administrator', 'Network Engineer', 'Security Administrator', 'Cloud Engineer',
            'DevOps Engineer', 'Site Reliability Engineer', 'IT Operations Manager',
            'Technical Support Engineer', 'Application Support Engineer', 'Systems Engineer',
            'IT Project Manager', 'IT Coordinator', 'Help Desk Manager', 'IT Security Analyst',
            'Cybersecurity Engineer', 'Information Security Officer', 'IT Director'
        ],
        'Legal' => [
            'Legal Advisor', 'Lawyer', 'Legal Consultant', 'Compliance Officer', 'Legal Associate',
            'Corporate Lawyer', 'Legal Counsel', 'Legal Manager', 'Paralegal', 'Legal Executive',
            'Contract Manager', 'Compliance Manager', 'Legal Analyst', 'Intellectual Property Lawyer',
            'Employment Lawyer', 'Tax Lawyer', 'Litigation Lawyer', 'General Counsel',
            'Chief Legal Officer', 'Legal Secretary', 'Legal Researcher', 'Regulatory Affairs Manager',
            'Risk & Compliance Manager', 'Data Protection Officer'
        ]
    ];

    private $jobTitleSkills = [
        // Management
        'Project Manager' => 'Project Management, Leadership, Communication, Risk Management, Agile, Scrum, Stakeholder Management, Budgeting',
        'Team Lead' => 'Leadership, Team Management, Communication, Problem Solving, Mentoring, Agile, Delegation',
        'Operations Manager' => 'Operations Management, Process Improvement, Leadership, Analytics, Planning, Supply Chain, Logistics',
        'HR Manager' => 'Recruitment, Employee Relations, Payroll, HR Policies, Training, Performance Management, Labor Laws',
        'Finance Manager' => 'Financial Planning, Budgeting, Accounting, Analysis, Reporting, Forecasting, Cash Flow Management',
        'General Manager' => 'Strategic Planning, Business Operations, P&L Management, Leadership, Decision Making, Team Building',
        'Branch Manager' => 'Branch Operations, Sales Management, Customer Service, Team Leadership, Business Development',
        'Assistant Manager' => 'Team Coordination, Operations Support, Customer Service, Reporting, Administrative Skills',
        'Department Head' => 'Department Management, Strategy, Budgeting, Team Leadership, Cross-functional Collaboration',
        'Business Manager' => 'Business Strategy, Operations, Financial Management, Stakeholder Management, Planning',
        'Program Manager' => 'Program Management, Multi-project Coordination, Strategic Planning, Risk Management, PMO',
        'Portfolio Manager' => 'Portfolio Management, Resource Allocation, Investment Strategy, Risk Assessment, Reporting',
        'Delivery Manager' => 'Delivery Management, Client Relations, Quality Assurance, Project Tracking, Team Coordination',
        'Product Manager' => 'Product Strategy, Roadmap Planning, Market Research, User Stories, Analytics, Stakeholder Management',
        'Account Manager' => 'Account Management, Client Relations, Sales, Negotiation, CRM, Business Development',
        'Regional Manager' => 'Regional Operations, Multi-site Management, Sales Strategy, Team Leadership, P&L Management',
        'Area Manager' => 'Area Operations, Territory Management, Sales, Team Leadership, Performance Monitoring',
        
        // Web Development
        'Frontend Developer' => 'HTML, CSS, JavaScript, React, Vue, Angular, Responsive Design, UI/UX, Git, REST API',
        'Backend Developer' => 'PHP, Laravel, Node.js, Python, Database Design, API Development, MySQL, MongoDB, Redis',
        'Full Stack Developer' => 'HTML, CSS, JavaScript, React, PHP, Laravel, Node.js, Database, API, Git, DevOps',
        'Web Developer' => 'HTML, CSS, JavaScript, jQuery, Web Design, SEO, CMS, Responsive Design, Git',
        'React Developer' => 'React, JavaScript, Redux, Context API, Hooks, REST API, Git, npm, Webpack',
        'Angular Developer' => 'Angular, TypeScript, RxJS, NgRx, HTML, CSS, REST API, Git, Node.js',
        'Vue.js Developer' => 'Vue.js, Vuex, JavaScript, HTML, CSS, REST API, Nuxt.js, Git, npm',
        'Node.js Developer' => 'Node.js, Express.js, MongoDB, MySQL, REST API, GraphQL, Microservices, Socket.io',
        'PHP Developer' => 'PHP, MySQL, Apache, Linux, MVC, OOP, REST API, Git, Composer',
        'Laravel Developer' => 'Laravel, PHP, MySQL, Eloquent ORM, REST API, MVC, Queue, Git, Redis',
        'WordPress Developer' => 'WordPress, PHP, MySQL, HTML, CSS, JavaScript, Plugin Development, Theme Development',
        'Drupal Developer' => 'Drupal, PHP, MySQL, Module Development, Theme Development, REST API, Git',
        'Python Developer' => 'Python, Django, Flask, PostgreSQL, REST API, OOP, Git, Linux',
        'Django Developer' => 'Django, Python, PostgreSQL, ORM, REST API, MVC, Celery, Git',
        'Ruby on Rails Developer' => 'Ruby, Rails, PostgreSQL, MVC, REST API, ActiveRecord, Git, RSpec',
        'ASP.NET Developer' => 'ASP.NET, C#, MVC, SQL Server, Entity Framework, Web API, Azure, Git',
        'Java Developer' => 'Java, Spring Boot, Hibernate, MySQL, REST API, Microservices, Maven, Git',
        'Spring Boot Developer' => 'Spring Boot, Java, Spring MVC, JPA, REST API, MySQL, Microservices, Maven',
        'Web Development Lead' => 'Full Stack Development, Team Leadership, Code Review, Architecture Design, Agile, Mentoring',
        'Senior Web Developer' => 'Full Stack Development, Architecture, Performance Optimization, Code Review, Mentoring',
        'Junior Web Developer' => 'HTML, CSS, JavaScript, Basic Programming, Git, Learning Mindset, Problem Solving',
        'Frontend Architect' => 'Frontend Architecture, Design Patterns, Performance, Scalability, React, Vue, Angular, Webpack',
        'Backend Architect' => 'Backend Architecture, Microservices, Database Design, API Design, Scalability, Cloud, Security',
        'DevOps Engineer' => 'CI/CD, Docker, Kubernetes, Jenkins, AWS, Azure, Linux, Git, Terraform, Ansible',
        'Web Performance Engineer' => 'Performance Optimization, Page Speed, Lighthouse, CDN, Caching, Profiling',
        'API Developer' => 'REST API, GraphQL, API Design, Documentation, Postman, Authentication, Rate Limiting',
        'Microservices Developer' => 'Microservices, Docker, Kubernetes, API Gateway, Event-driven Architecture, Service Mesh',
        
        // Testing
        'QA Engineer' => 'Manual Testing, Test Cases, Bug Reporting, Quality Assurance, Documentation, JIRA, TestRail',
        'Test Automation Engineer' => 'Selenium, Automation Testing, Python, Java, Test Frameworks, CI/CD, Git',
        'Manual Tester' => 'Manual Testing, Test Planning, Bug Tracking, Quality Assurance, Test Execution',
        'QA Lead' => 'QA Management, Test Strategy, Leadership, Automation, Reporting, Team Coordination',
        'Performance Tester' => 'Performance Testing, Load Testing, JMeter, LoadRunner, Analysis, Optimization',
        'Software Tester' => 'Software Testing, Test Cases, Bug Reporting, Regression Testing, Exploratory Testing',
        'QA Analyst' => 'Quality Analysis, Requirements Analysis, Test Design, Defect Management, Documentation',
        'Test Engineer' => 'Test Engineering, Test Automation, Test Planning, Defect Tracking, CI/CD',
        'Quality Analyst' => 'Quality Assurance, Process Improvement, Root Cause Analysis, Metrics, Reporting',
        'Automation Tester' => 'Test Automation, Selenium, Appium, Cucumber, TestNG, JUnit, Git',
        'Senior QA Engineer' => 'Advanced Testing, Test Strategy, Automation, Mentoring, Code Review, CI/CD',
        'Junior QA Engineer' => 'Manual Testing, Test Basics, Bug Reporting, Learning, Documentation',
        'QA Manager' => 'QA Management, Team Leadership, Test Strategy, Process Improvement, Metrics, Budgeting',
        'Test Lead' => 'Test Management, Team Leadership, Test Planning, Coordination, Reporting',
        'Security Tester' => 'Security Testing, Penetration Testing, Vulnerability Assessment, OWASP, Ethical Hacking',
        'Penetration Tester' => 'Penetration Testing, Ethical Hacking, Security Tools, Vulnerability Analysis, Reporting',
        'Load Tester' => 'Load Testing, Performance Testing, JMeter, LoadRunner, Stress Testing, Analysis',
        'API Tester' => 'API Testing, REST API, SOAP, Postman, SoapUI, Automation, JSON, XML',
        'Mobile App Tester' => 'Mobile Testing, iOS, Android, Appium, Manual Testing, Device Testing',
        'Web Application Tester' => 'Web Testing, Browser Testing, Cross-browser, Responsive Testing, Selenium',
        'Database Tester' => 'Database Testing, SQL, Data Validation, ETL Testing, Performance',
        'ETL Tester' => 'ETL Testing, Data Warehouse, SQL, Data Validation, Informatica, Talend',
        'UAT Coordinator' => 'User Acceptance Testing, Coordination, Documentation, Stakeholder Management',
        'Test Analyst' => 'Test Analysis, Requirements Analysis, Test Design, Documentation, Reporting',
        'SDET' => 'Software Development, Test Automation, Programming, CI/CD, Test Framework Development',
        'Quality Assurance Specialist' => 'Quality Assurance, Process Improvement, Testing, Documentation, Compliance',
        
        // Designing
        'UI Designer' => 'UI Design, Figma, Adobe XD, Sketch, Prototyping, User Interface, Visual Design, Typography',
        'UX Designer' => 'UX Design, User Research, Wireframing, Prototyping, Figma, User Journey, Information Architecture',
        'Graphic Designer' => 'Graphic Design, Adobe Creative Suite, Photoshop, Illustrator, Branding, Visual Design, Typography',
        'Product Designer' => 'Product Design, UX/UI, Figma, User Research, Prototyping, Design Thinking, Visual Design',
        'Web Designer' => 'Web Design, HTML, CSS, Figma, Responsive Design, Adobe XD, Visual Design',
        'Visual Designer' => 'Visual Design, Typography, Color Theory, Layout Design, Adobe Creative Suite, Branding',
        'Interaction Designer' => 'Interaction Design, Prototyping, User Flows, Micro-interactions, Animation, Figma',
        'UI/UX Designer' => 'UI/UX Design, Figma, Adobe XD, Prototyping, User Research, Wireframing, Visual Design',
        'Motion Graphics Designer' => 'Motion Graphics, After Effects, Animation, Video Editing, Cinema 4D, Visual Effects',
        'Brand Designer' => 'Brand Design, Brand Identity, Logo Design, Brand Guidelines, Visual Design, Typography',
        'Logo Designer' => 'Logo Design, Brand Identity, Adobe Illustrator, Typography, Visual Design, Branding',
        'Illustration Designer' => 'Illustration, Digital Art, Adobe Illustrator, Procreate, Visual Storytelling',
        'Creative Designer' => 'Creative Design, Adobe Creative Suite, Concept Development, Visual Design, Branding',
        'Digital Designer' => 'Digital Design, UI/UX, Web Design, Mobile Design, Adobe Creative Suite, Figma',
        '3D Designer' => '3D Design, 3D Modeling, Blender, Maya, Cinema 4D, Texturing, Rendering',
        'Game Designer' => 'Game Design, Level Design, Unity, Unreal Engine, Game Mechanics, Storytelling',
        'Art Director' => 'Art Direction, Creative Leadership, Visual Strategy, Team Management, Adobe Creative Suite',
        'Senior Designer' => 'Advanced Design, Mentoring, Design Systems, Leadership, Cross-functional Collaboration',
        'Junior Designer' => 'Basic Design, Adobe Creative Suite, Learning, Creativity, Attention to Detail',
        'Design Lead' => 'Design Leadership, Team Management, Design Strategy, Mentoring, Cross-functional Collaboration',
        'UX Researcher' => 'User Research, Usability Testing, Interviews, Surveys, Data Analysis, Research Methods',
        'UX Writer' => 'UX Writing, Content Strategy, Microcopy, User-centered Writing, Information Architecture',
        'Design Manager' => 'Design Management, Team Leadership, Design Strategy, Project Management, Resource Planning',
        'Print Designer' => 'Print Design, Adobe InDesign, Typography, Layout Design, Color Management, Pre-press',
        'Packaging Designer' => 'Packaging Design, 3D Mockups, Adobe Creative Suite, Print Production, Die-cutting',
    ];

    public function index()
    {
        return redirect()->route('recruitment.index', ['tab' => 'job-requests']);
    }

    public function create()
    {
        $department = DB::table('department')->get();
        $experiences = DB::table('experience')->where('status', 'active')->get();

        return view('hrms.Jobs.job-vacancy-requests.create', compact('department', 'experiences'));
    }

public function getJobTitlesByDepartment(Request $request)
{
    $departmentIdentifier = $request->input('department');
    
    // Check if it's an ID (numeric) or name (string)
    if (is_numeric($departmentIdentifier)) {
        // Fetch department name from database
        $department = DB::table('department')->where('id', $departmentIdentifier)->first();
        $departmentName = $department ? $department->department : null;
    } else {
        $departmentName = $departmentIdentifier;
    }
    
    // Use the static array with department name
    $jobTitles = $this->departmentJobTitles[$departmentName] ?? [];
    
    return response()->json(['titles' => $jobTitles]);
}
  public function getSkillsByJobTitle(Request $request)
{
    $jobTitle = $request->input('job_title');
    
    // Use the static array instead of database query
    $skills = $this->jobTitleSkills[$jobTitle] ?? '';
    
    return response()->json(['skills' => $skills]);
}public function store(Request $request)
{
    $validatedData = $this->validateRequest($request);
    $branchId = Session::get('branch_id');

    DB::table('job_vacancy_requests')->insert([
        'job_title' => $validatedData['job_title'],
        'department' => $validatedData['department'],
        'job_location' => $validatedData['job_location'],
        'vacancies' => $validatedData['vacancies'],
        'experience' => $validatedData['experience'],
        'salary_from' => $request->input('salary_from'),  // Use input() method which returns null if not present
        'salary_to' => $request->input('salary_to'),
        'job_type' => $request->input('job_type'),
        'status' => $request->input('status'),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'description' => $request->input('description'),
        'screening_questions' => $request->input('screening_questions'),
        'skills' => $validatedData['skills'],
        'approval_status' => 'pending',
        'hr_approval_status' => 'pending',
        'branch_id' => $branchId,
        'created_at' => now(),
        'updated_at' => now(),
        'deleted_at' => 0,
    ]);

    return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
        ->with('success', 'Job vacancy request created successfully.');
}
    public function edit($id)
    {
        $department = DB::table('department')->get();
        $experiences = DB::table('experience')->where('status', 'active')->get();
        $jobRequest = DB::table('job_vacancy_requests')->where('id', $id)->first();

        if (!$jobRequest) {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'Job vacancy request not found.');
        }

        if ($jobRequest->start_date) {
            $jobRequest->start_date = Carbon::parse($jobRequest->start_date)->format('Y-m-d');
        }
        if ($jobRequest->end_date) {
            $jobRequest->end_date = Carbon::parse($jobRequest->end_date)->format('Y-m-d');
        }

        return view('hrms.Jobs.job-vacancy-requests.edit', compact('jobRequest', 'department', 'experiences'));
    }
public function update(Request $request, $id)
{
    $jobRequest = DB::table('job_vacancy_requests')->where('id', $id)->first();

    if (!$jobRequest) {
        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('error', 'Job vacancy request not found.');
    }

    if ($jobRequest->hr_approval_status === 'approved') {
        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('error', 'Approved job vacancy requests cannot be edited.');
    }

    $validatedData = $this->validateRequest($request);

    DB::table('job_vacancy_requests')->where('id', $id)->update([
        'job_title' => $validatedData['job_title'],
        'department' => $validatedData['department'],
        'job_location' => $validatedData['job_location'],
        'vacancies' => $validatedData['vacancies'],
        'experience' => $validatedData['experience'],
        'salary_from' => $request->input('salary_from'),
        'salary_to' => $request->input('salary_to'),
        'job_type' => $request->input('job_type'),
        'status' => $request->input('status'),
        'start_date' => $request->input('start_date'),
        'end_date' => $request->input('end_date'),
        'description' => $request->input('description'),
        'screening_questions' => $request->input('screening_questions'),
        'skills' => $validatedData['skills'],
        'updated_at' => now(),
    ]);

    return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
        ->with('success', 'Job vacancy request updated successfully.');
}
    public function destroy($id)
    {
        DB::table('job_vacancy_requests')->where('id', $id)->delete();

        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('success', 'Job vacancy request deleted successfully.');
    }

    public function approve($id)
    {
        $jobRequest = DB::table('job_vacancy_requests')->where('id', $id)->first();

        if (!$jobRequest) {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'Job vacancy request not found.');
        }

        if ($jobRequest->approval_status !== 'pending') {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'This request has already been processed by the manager.');
        }

        DB::table('job_vacancy_requests')->where('id', $id)->update([
            'approval_status' => 'approved',
            'updated_at' => now(),
        ]);

        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('success', 'Manager approval completed. HR approval is now available.');
    }

    public function hrApprove($id)
    {
        $jobRequest = DB::table('job_vacancy_requests')->where('id', $id)->first();

        if (!$jobRequest) {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'Job vacancy request not found.');
        }

        if ($jobRequest->approval_status !== 'approved') {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'Manager approval is required before HR approval.');
        }

        if ($jobRequest->converted_job_id) {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'This request is already converted to a job.');
        }

        $jobId = DB::table('managejobs')->insertGetId([
            'job_title' => $jobRequest->job_title,
            'department' => $jobRequest->department,
            'job_location' => $jobRequest->job_location,
            'vacancies' => $jobRequest->vacancies,
            'experience' => $jobRequest->experience,
            'salary_from' => $jobRequest->salary_from,
            'salary_to' => $jobRequest->salary_to,
            'job_type' => $jobRequest->job_type,
            'status' => $jobRequest->status,
            'start_date' => $jobRequest->start_date,
            'end_date' => $jobRequest->end_date,
            'description' => $jobRequest->description,
            'screening_questions' => $jobRequest->screening_questions ?? null,
            'branch_id' => $jobRequest->branch_id,
            'skills' => $jobRequest->skills,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => 0,
        ]);

        DB::table('job_vacancy_requests')->where('id', $id)->update([
            'hr_approval_status' => 'approved',
            'converted_job_id' => $jobId,
            'approved_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('success', 'HR approved the request and converted it to a job.');
    }

    public function reject($id)
    {
        $jobRequest = DB::table('job_vacancy_requests')->where('id', $id)->first();

        if (!$jobRequest) {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'Job vacancy request not found.');
        }

        $updateData = [
            'updated_at' => now(),
        ];

        if ($jobRequest->approval_status === 'pending') {
            $updateData['approval_status'] = 'rejected';
            $updateData['hr_approval_status'] = 'rejected';
        } elseif ($jobRequest->approval_status === 'approved' && $jobRequest->hr_approval_status === 'pending') {
            $updateData['hr_approval_status'] = 'rejected';
        } else {
            return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
                ->with('error', 'This request cannot be rejected now.');
        }

        DB::table('job_vacancy_requests')->where('id', $id)->update($updateData);

        return redirect()->route('recruitment.index', ['tab' => 'job-requests'])
            ->with('success', 'Job vacancy request rejected.');
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'job_title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'job_location' => 'required|string|max:255',
            'vacancies' => 'required|integer|min:1',
            'experience' => 'required|string|max:255',
         
        
         
            'screening_questions' => 'nullable|string',
            'skills' => 'required|string|max:500',
        ]);
    }
}
