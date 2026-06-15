@extends('layouts.index')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('templates.index') }}" class="btn btn-link me-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h1 class="h3 mb-0">{{ $template->name }}</h1>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center py-4">
                <h5 class="text-muted">Total Questions</h5>
                <h2 class="text-primary">{{ $template->total_questions }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center py-4">
                <h5 class="text-muted">Metrics Used</h5>
                <h2 class="text-info">{{ $template->metrics()->count() }}</h2>
            </div>
        </div>
    </div>

    @if($template->metrics()->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Metrics Used</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @foreach($template->metrics as $metric)
                <span class="badge bg-light text-dark" style="padding: 8px 12px; font-size: 13px;">
                    {{ $metric->name }}
                </span>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Questions</h5>
        </div>
        <div class="card-body">
            @forelse($template->questions as $index => $question)
            <div class="card mb-3 border-0 bg-light">
                <div class="card-body">
                    <div class="d-flex gap-3">
                        <div class="question-icon" style="width: 40px; text-align: center;">
                            @switch($question->type)
                                @case('yes_no')
                                    <i class="fas fa-check-square fa-lg text-success"></i>
                                    @break
                                @case('nps')
                                    <i class="fas fa-thumbs-up fa-lg text-primary"></i>
                                    @break
                                @case('star')
                                    <i class="fas fa-star fa-lg text-warning"></i>
                                    @break
                                @case('rating_scale')
                                    <i class="fas fa-sliders-h fa-lg text-info"></i>
                                    @break
                                @case('single')
                                    <i class="fas fa-circle fa-lg text-secondary"></i>
                                    @break
                                @case('multiple')
                                    <i class="fas fa-check-circle fa-lg text-secondary"></i>
                                    @break
                                @case('comment')
                                    <i class="fas fa-comment fa-lg text-warning"></i>
                                    @break
                                @case('date')
                                    <i class="fas fa-calendar fa-lg text-danger"></i>
                                    @break
                            @endswitch
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $question->question_text }}</h6>
                            <small class="text-muted">
                                Type: <strong>{{ ucfirst(str_replace('_', ' ', $question->type)) }}</strong>
                                @if($question->metric)
                                    | Metric: <strong>{{ $question->metric->name }}</strong>
                                @endif
                                @if($question->is_mandatory)
                                    | <span class="badge bg-danger">Mandatory</span>
                                @endif
                            </small>

                            @if($question->type == 'single' || $question->type == 'multiple' || $question->type == 'rating_scale' || $question->type == 'nps')
                            <div class="mt-2">
                                <small class="text-muted d-block mb-1">Options:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($question->answers as $answer)
                                    <span class="badge bg-secondary">{{ $answer->label }}</span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('templates.edit', $template->id) }}?question={{ $question->id }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="alert alert-info">No questions added to this template yet.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i> Edit Template
        </a>
        <a href="{{ route('templates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Templates
        </a>
    </div>
</div>
@endsection
