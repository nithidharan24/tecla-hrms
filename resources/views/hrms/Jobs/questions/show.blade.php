@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Question Details</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Question.index') }}">Questions</a></li>
                    <li class="breadcrumb-item active">Details</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('Question.edit', $question->id) }}" class="btn add-btn">
                    <i class="fa-solid fa-pencil m-r-5"></i> Edit Question
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="card-title">Question #{{ $question->id }}</h4>
                    <p class="card-text lead">{!! nl2br(e($question->question)) !!}</p>

                    <div class="badge bg-primary mb-4">Category: {{ $question->category }}</div>

                    <h5 class="mt-4">Options:</h5>
                    <ul class="list-group list-group-flush" id="optionsList">
                        <li class="list-group-item option-item" data-correct="A">
                            <strong>A:</strong> {{ $question->option_a }}
                        </li>
                        <li class="list-group-item option-item" data-correct="B">
                            <strong>B:</strong> {{ $question->option_b }}
                        </li>
                        <li class="list-group-item option-item" data-correct="C">
                            <strong>C:</strong> {{ $question->option_c }}
                        </li>
                        <li class="list-group-item option-item" data-correct="D">
                            <strong>D:</strong> {{ $question->option_d }}
                        </li>
                    </ul>

                    <!-- View Result Button -->
                    <div class="mt-4">
                        <button type="button" id="viewResultBtn" class="btn btn-primary">
                            <i class="fa-solid fa-eye m-r-5"></i> View Result
                        </button>
                    </div>

                    <!-- Result Section (Initially Hidden) -->
                    <div id="resultSection" class="mt-4" style="display: none;">
                        <div class="alert alert-success">
                            <strong>Correct Answer:</strong> {{ $question->correct_answer }}
                        </div>

                        @if($question->answer_explanation)
                            <h5 class="mt-4">Answer Explanation:</h5>
                            <div class="card card-body bg-light">
                                <p>{!! nl2br(e($question->answer_explanation)) !!}</p>
                            </div>
                        @endif
                    </div>

                    @if($question->code_snippets)
                        <h5 class="mt-4">Code Snippets:</h5>
                        <pre class="bg-dark text-white p-3 rounded"><code>{!! e($question->code_snippets) !!}</code></pre>
                    @endif

                    @if($question->video_link)
                        <h5 class="mt-4">Video Explanation:</h5>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="{{ str_replace('watch?v=', 'embed/', $question->video_link) }}" allowfullscreen></iframe>
                        </div>
                    @endif

                    <div class="mt-4 text-muted">
                        <small>Created: {{ \Carbon\Carbon::parse($question->created_at)->format('d M Y, H:i') }}</small><br>
                        <small>Last Updated: {{ \Carbon\Carbon::parse($question->updated_at)->format('d M Y, H:i') }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    @if($question->question_image)
                        <div class="card mt-4 mt-md-0">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Question Image</h5>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ asset($question->question_image) }}" alt="Question Image" class="img-fluid rounded shadow-sm">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 text-end">
                <a href="{{ route('Question.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left m-r-5"></i> Back to Questions
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript to handle the View Result functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewResultBtn = document.getElementById('viewResultBtn');
    const resultSection = document.getElementById('resultSection');
    const optionsList = document.getElementById('optionsList');
    const optionItems = document.querySelectorAll('.option-item');
    
    // Store the correct answer from the server
    const correctAnswer = '{{ $question->correct_answer }}';
    
    viewResultBtn.addEventListener('click', function() {
        // Show the result section
        resultSection.style.display = 'block';
        
        // Highlight the correct answer
        optionItems.forEach(item => {
            if (item.getAttribute('data-correct') === correctAnswer) {
                item.classList.add('bg-success', 'text-white');
            }
        });
        
        // Disable the button after first click
        this.disabled = true;
        this.innerHTML = '<i class="fa-solid fa-check m-r-5"></i> Result Revealed';
        this.classList.remove('btn-primary');
        this.classList.add('btn-success');
    });
});
</script>

<style>
.option-item {
    transition: all 0.3s ease;
}

.option-item:hover {
    background-color: #f8f9fa;
}
</style>
@endsection