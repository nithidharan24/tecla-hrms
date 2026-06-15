
@extends('layouts.index')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-flex align-items-center">
                <i class="fas fa-layer-group me-2 text-primary"></i> Survey Templates
            </h1>
            <p class="text-muted">Use templates to simplify your survey creation with pre-designed and custom survey templates</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('templates.create') }}" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i> Add Engagement Template
            </a>
        </div>
    </div>

    @if($templates->count() > 0)
    <div class="row">
        @foreach($templates as $template)
        @php
            // Dynamic gradient colors based on template
           $gradients = [
    ['#a78bfa', '#6d28d9'],
    ['#f472b6', '#db2777'],
    ['#60a5fa', '#2563eb'],
    ['#34d399', '#059669'],
    ['#fbbf24', '#d97706'],
    ['#38bdf8', '#0ea5e9']
];

            $gradient = $gradients[$loop->index % count($gradients)];
            $questionsByType = $template->questions->groupBy('type');
        @endphp
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="template-card card h-100 shadow-sm border-0 overflow-hidden">
                <!-- Gradient Header -->
                <div class="template-header position-relative" style="height: 140px; background: linear-gradient(135deg, {{ $gradient[0] }} 0%, {{ $gradient[1] }} 100%);">
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-white text-dark shadow-sm">
                            <i class="fas fa-list-check me-1"></i> {{ $template->total_questions }}
                        </span>
                    </div>
                    <div class="position-absolute bottom-0 start-0 p-3">
                        <h5 class="text-white mb-1 fw-bold">{{ Str::limit($template->name, 25) }}</h5>
                        <small class="text-white opacity-75">{{ ucfirst(str_replace('_', ' ', $template->survey_flow)) }}</small>
                    </div>
                    
                    <!-- Decorative elements -->
                    <div class="position-absolute top-50 start-0 translate-middle-y" style="width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%; left: -50px;"></div>
                    <div class="position-absolute bottom-0 end-0" style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%; transform: translate(40px, 40px);"></div>
                </div>
                
                <!-- Card Body -->
                <div class="card-body">
                    <p class="card-text text-muted mb-3">{{ Str::limit($template->description, 100) }}</p>
                    
                    <!-- Question Types Preview -->
                    <div class="question-types mb-4">
                        <small class="d-block mb-2 text-muted fw-semibold">Question Types:</small>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($questionsByType->take(4) as $type => $questions)
                            <div class="question-type-pill d-flex align-items-center gap-1">
                                <span class="question-type-icon">
                                    @switch($type)
                                        @case('yes_no')<i class="fas fa-check-square text-success"></i>@break
                                        @case('nps')<i class="fas fa-thumbs-up text-primary"></i>@break
                                        @case('star')<i class="fas fa-star text-warning"></i>@break
                                        @case('rating_scale')<i class="fas fa-sliders-h text-info"></i>@break
                                        @case('single')<i class="fas fa-circle-dot text-secondary"></i>@break
                                        @case('multiple')<i class="fas fa-check-double text-secondary"></i>@break
                                        @case('comment')<i class="fas fa-comment-dots text-warning"></i>@break
                                        @case('date')<i class="fas fa-calendar-day text-danger"></i>@break
                                    @endswitch
                                </span>
                                <span class="badge bg-light text-dark">
                                    {{ $questions->count() }} {{ Str::limit(ucfirst(str_replace('_', ' ', $type)), 12) }}
                                </span>
                            </div>
                            @endforeach
                            @if($questionsByType->count() > 4)
                            <span class="badge bg-light text-dark">
                                +{{ $questionsByType->count() - 4 }} more
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Template Stats -->
                    <div class="template-stats d-flex justify-content-between border-top pt-3">
                        <div class="text-center">
                            <div class="fw-bold text-primary">{{ $template->total_questions }}</div>
                            <small class="text-muted">Questions</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-success">{{ $template->questions->where('is_mandatory', true)->count() }}</div>
                            <small class="text-muted">Required</small>
                        </div>
                        <div class="text-center">
                            <div class="fw-bold text-info">
                                @if($template->layout == 'one_per_page')
                                    <i class="fas fa-file-alt"></i>
                                @else
                                    <i class="fas fa-layer-group"></i>
                                @endif
                            </div>
                            <small class="text-muted">{{ $template->layout == 'one_per_page' ? 'Multi-page' : 'Single Page' }}</small>
                        </div>
                    </div>
                </div>
                
                <!-- Card Footer -->
                <div class="card-footer bg-transparent border-top-0 pt-0">
                    <div class="d-flex gap-2">
                        <a href="{{ route('templates.show', $template->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('templates.destroy', $template->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this template?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Empty state for no templates -->
    @else
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="empty-state-icon mb-4">
                        <div class="position-relative d-inline-block">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-4">
                                <i class="fas fa-layer-group fa-4x text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-dark mb-3">No templates yet</h4>
                    <p class="text-muted mb-4">Start by creating your first survey template to streamline your survey creation process.</p>
                    <a href="{{ route('templates.create') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-plus me-2"></i> Create Your First Template
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.template-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 12px;
    overflow: hidden;
}

.template-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.template-header {
    position: relative;
    overflow: hidden;
}

.question-type-pill {
    background: rgba(var(--bs-light-rgb), 0.7);
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.question-type-icon {
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.template-stats > div {
    flex: 1;
}

.template-stats > div:not(:last-child) {
    border-right: 1px solid var(--bs-border-color);
}

.btn-outline-primary, .btn-outline-secondary, .btn-outline-danger {
    transition: all 0.2s ease;
    border-width: 1px;
}

.btn-outline-primary:hover {
    background-color: var(--bs-primary);
    color: white;
}

.btn-outline-secondary:hover {
    background-color: var(--bs-secondary);
    color: white;
}

.btn-outline-danger:hover {
    background-color: var(--bs-danger);
    color: white;
}

.badge.bg-light {
    backdrop-filter: blur(10px);
    background: rgba(255,255,255,0.9) !important;
}
.text-white{
    font-size: 20px;
}
</style>
@endsection