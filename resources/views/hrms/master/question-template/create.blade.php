@extends('layouts.index')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('templates.index') }}" class="btn btn-link me-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Create New Template</h1>
            </div>

            <!-- Only 2 steps -->
            <div class="wizard-steps mb-4">
                <div class="step-indicator active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-label">Setup</div>
                </div>
                <div class="step-line"></div>
                <div class="step-indicator" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-label">Questions</div>
                </div>
            </div>

            <form id="templateForm" action="{{ route('templates.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                
                <!-- Hidden fields for questions -->
                <input type="hidden" name="questions" id="questionsInput" value="">

                <!-- Step 1: Setup -->
                <div class="form-step active" data-step="1">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Template Details</h5>

                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="e.g., Employee Engagement Survey" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Give your template a name and a description"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Survey Flow <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="survey_flow" id="flow_single" value="single_metric" checked>
                                    <label class="form-check-label" for="flow_single">
                                        Single Metric Survey
                                        <small class="d-block text-muted">A survey with only one metric. Add questions associated with the selected metric.</small>
                                    </label>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Layout Preference <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout" id="layout_one" value="one_per_page">
                                    <label class="form-check-label" for="layout_one">One question per page</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout" id="layout_all" value="all_in_one" checked>
                                    <label class="form-check-label" for="layout_all">All questions in one page</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Integrated Question Builder -->
                <div class="form-step" data-step="2" style="display:none;">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add Questions</h5>
                            <div>
                                <span id="questionCount" class="badge bg-primary me-2">0 Questions</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Left Sidebar: Question Types -->
                                <div class="col-md-3">
                                    <h6 class="mb-3 fw-bold">Question Types</h6>
                                    <div id="questionTypes" class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="yes_no">
                                            <i class="fas fa-check-square me-2"></i> Yes/No
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="nps">
                                            <i class="fas fa-thumbs-up me-2"></i> NPS
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="star">
                                            <i class="fas fa-star me-2"></i> Star
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="rating_scale">
                                            <i class="fas fa-sliders-h me-2"></i> Rating Scale
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="single">
                                            <i class="fas fa-circle me-2"></i> Single Choice
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="multiple">
                                            <i class="fas fa-check-circle me-2"></i> Multiple Choice
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="comment">
                                            <i class="fas fa-comment me-2"></i> Comment
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action question-type-btn" data-type="date">
                                            <i class="fas fa-calendar me-2"></i> Date
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Side: Question Builder & List -->
                                <div class="col-md-9">
                                    <!-- Question Builder Form -->
                                    <div id="questionBuilderForm" class="card bg-light mb-4" style="display:none;">
                                        <div class="card-body">
                                            <h6 class="mb-3">Add New Question</h6>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Question Text <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="newQuestionText" rows="2" placeholder="Enter your question here..."></textarea>
                                            </div>

                                            <!-- Options Section -->
                                            <div id="optionsSection" style="display:none;">
                                                <label class="form-label">Options <span class="text-danger">*</span></label>
                                                <div id="optionsList" class="mb-3"></div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="addOptionBtn">
                                                    <i class="fas fa-plus me-1"></i> Add Option
                                                </button>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-primary" id="saveQuestionBtn">
                                                    <i class="fas fa-plus me-1"></i> Add Question
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="cancelQuestionBtn">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Questions List -->
                                    <div id="questionsList" class="questions-list">
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <p>No questions added yet. Select a question type to get started.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation buttons - IMPORTANT: Keep the submit button here -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
                        <i class="fas fa-arrow-left me-2"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                        <i class="fas fa-save me-2"></i> Create Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.wizard-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.step-indicator {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    background: #e9ecef;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-weight: bold;
    text-align: center;
    transition: all 0.3s ease;
}

.step-indicator.active {
    background: #e66c18;
    color: white;
    border-color: #e66c18;
}

.step-number {
    font-size: 20px;
}

.step-label {
    font-size: 12px;
    margin-top: 4px;
}

.step-line {
    flex: 1;
    height: 2px;
    background: #dee2e6;
    margin: 0 10px;
}

.form-step {
    display: none;
    animation: fadeIn 0.3s ease;
}

.form-step.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.question-type-btn {
    transition: all 0.2s ease;
    border: none;
    text-align: left;
}

.question-type-btn:hover {
    background-color: #e9ecef;
}

.question-type-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.color-option-label {
    display: block;
    cursor: pointer;
}

.color-option {
    transition: all 0.2s ease;
    border: 3px solid transparent !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-input:checked ~ .color-option {
    border-color: #000 !important;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25), 0 2px 4px rgba(0,0,0,0.1) !important;
}

.question-card {
    transition: all 0.2s ease;
}

.question-card:hover {
    background-color: #f8f9fa;
}
</style>

<script>
let currentStep = 1;
let questions = [];
let selectedType = null;
let questionOptions = [];

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('[v0] Page loaded - current step:', currentStep);
    
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);
    document.getElementById('templateForm').addEventListener('submit', submitForm);

    // Color selection
    document.querySelectorAll('.color-input').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.style.borderColor = 'transparent';
            });
        });
    });

    // Question type selection
    document.querySelectorAll('.question-type-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            selectedType = btn.dataset.type;
            document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show question builder
            document.getElementById('questionBuilderForm').style.display = 'block';
            document.getElementById('newQuestionText').value = '';
            questionOptions = [];
            
            // Show options section for types that need them
            const typesWithOptions = ['single', 'multiple', 'rating_scale', 'nps', 'star'];
            document.getElementById('optionsSection').style.display = typesWithOptions.includes(selectedType) ? 'block' : 'none';
            renderOptions();
            
            // Focus on question text
            document.getElementById('newQuestionText').focus();
        });
    });

    // Add option
    document.getElementById('addOptionBtn').addEventListener('click', () => {
        const label = prompt('Enter option label:');
        if (!label) return;
        questionOptions.push(label);
        renderOptions();
    });

    // Save question
    document.getElementById('saveQuestionBtn').addEventListener('click', () => {
        const questionText = document.getElementById('newQuestionText').value.trim();
        if (!questionText) {
            alert('Please enter a question');
            return;
        }

        const typesWithOptions = ['single', 'multiple', 'rating_scale', 'nps', 'star'];
        if (typesWithOptions.includes(selectedType) && questionOptions.length === 0) {
            alert('Please add at least one option');
            return;
        }

        // Create question object
        const question = {
            id: Date.now(),
            type: selectedType,
            text: questionText,
            options: [...questionOptions]
        };

        questions.push(question);
        console.log('[v0] Question added:', question);
        
        // Clear and hide builder
        document.getElementById('questionBuilderForm').style.display = 'none';
        document.getElementById('newQuestionText').value = '';
        questionOptions = [];
        document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
        
        // Render questions
        renderQuestions();
    });

    // Cancel question builder
    document.getElementById('cancelQuestionBtn').addEventListener('click', () => {
        document.getElementById('questionBuilderForm').style.display = 'none';
        document.getElementById('newQuestionText').value = '';
        questionOptions = [];
        document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
    });
});

function nextStep() {
    console.log('[v0] Next button clicked, current step:', currentStep);
    if (validateStep(currentStep)) {
        currentStep++;
        console.log('[v0] Moving to step:', currentStep);
        updateSteps();
    }
}

function prevStep() {
    console.log('[v0] Previous button clicked, current step:', currentStep);
    currentStep--;
    updateSteps();
}

function updateSteps() {
    console.log('[v0] Updating steps to:', currentStep);
    
    // Update form steps visibility
    document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
        step.style.display = 'none';
    });
    
    const activeStep = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    if (activeStep) {
        activeStep.classList.add('active');
        activeStep.style.display = 'block';
    }

    // Update step indicators
    document.querySelectorAll('.step-indicator').forEach(step => step.classList.remove('active'));
    const activeIndicator = document.querySelector(`.step-indicator[data-step="${currentStep}"]`);
    if (activeIndicator) {
        activeIndicator.classList.add('active');
    }

    // Update navigation buttons
    document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'block';
    document.getElementById('nextBtn').style.display = currentStep === 2 ? 'none' : 'block';
    document.getElementById('submitBtn').style.display = currentStep === 2 ? 'block' : 'none';
}

function validateStep(step) {
    if (step === 1) {
        const name = document.getElementById('name').value.trim();
        if (!name) {
            alert('Please enter a template name');
            return false;
        }
        return true;
    }
    if (step === 2) {
        return true;
    }
    return true;
}

function renderOptions() {
    const optionsList = document.getElementById('optionsList');
    optionsList.innerHTML = questionOptions.map((option, index) => `
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">${index + 1}</span>
            <input type="text" class="form-control" value="${option}" disabled>
            <button type="button" class="btn btn-outline-danger btn-sm remove-option-btn" data-index="${index}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
    
    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-option-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            questionOptions.splice(index, 1);
            renderOptions();
        });
    });
}

function renderQuestions() {
    const questionsList = document.getElementById('questionsList');
    const questionCount = document.getElementById('questionCount');
    
    questionCount.textContent = `${questions.length} Questions`;
    
    if (questions.length === 0) {
        questionsList.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>No questions added yet. Select a question type to get started.</p>
            </div>
        `;
        return;
    }
    
    questionsList.innerHTML = questions.map(q => `
        <div class="card mb-2 question-card" data-id="${q.id}">
            <div class="card-body py-2">
                <div class="d-flex gap-3 align-items-start">
                    <div class="question-number fw-bold text-orange">${questions.indexOf(q) + 1}</div>
                    <div class="flex-grow-1">
                        <p class="mb-1"><strong>${q.text}</strong></p>
                        <small class="text-muted">Type: ${q.type}</small>
                        ${q.options && q.options.length > 0 ? `
                            <div class="mt-2">
                                <small class="text-muted">Options:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    ${q.options.map(opt => `<span class="badge bg-light text-dark">${opt}</span>`).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" data-id="${q.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = parseInt(this.dataset.id);
            questions = questions.filter(q => q.id !== id);
            renderQuestions();
        });
    });
}

function submitForm(e) {
    e.preventDefault();
    console.log('[v0] Form submission started');
    
    const name = document.getElementById('name').value.trim();
    if (!name) {
        alert('Please enter a template name');
        currentStep = 1;
        updateSteps();
        document.getElementById('name').focus();
        return;
    }
    
    console.log('[v0] Questions to submit:', questions);
    console.log('[v0] Questions count:', questions.length);
    
    document.getElementById('questionsInput').value = JSON.stringify(questions);
    
    console.log('[v0] Form data ready, submitting...');
    console.log('[v0] Hidden input value:', document.getElementById('questionsInput').value);
    
    // Submit form
    document.getElementById('templateForm').submit();
}

// Initial update
updateSteps();
</script>
@endsection
