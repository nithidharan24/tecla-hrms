
@extends('layouts.index')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('templates.index') }}" class="btn btn-link me-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="h3 mb-0">Edit Template: {{ $template->name }}</h1>
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

            <form id="templateForm" action="{{ route('templates.update', $template->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf @method('PUT')
                
                <!-- Hidden fields for questions -->
                <input type="hidden" name="questions" id="questionsInput" value="{{ json_encode($template->questions->map(function($q) {
                    return [
                        'id' => $q->id,
                        'type' => $q->type,
                        'text' => $q->question_text,
                        'is_mandatory' => $q->is_mandatory,
                        'enable_comments' => $q->enable_comments,
                        'options' => $q->answers->pluck('label')->toArray()
                    ];
                })) }}">

                <!-- Step 1: Setup -->
                <div class="form-step active" data-step="1">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Template Details</h5>

                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $template->name }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $template->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Survey Flow <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="survey_flow" id="flow_single" value="single_metric" {{ $template->survey_flow == 'single_metric' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flow_single">
                                        Single Metric Survey
                                        <small class="d-block text-muted">A survey with only one metric. Add questions associated with the selected metric.</small>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="survey_flow" id="flow_multi" value="multi_metric" {{ $template->survey_flow == 'multi_metric' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flow_multi">
                                        Multi Metric Survey
                                        <small class="d-block text-muted">A survey with multiple metrics. Add questions for each metric.</small>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Layout Preference <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout" id="layout_one" value="one_per_page" {{ $template->layout == 'one_per_page' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="layout_one">One question per page</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="layout" id="layout_all" value="all_in_one" {{ $template->layout == 'all_in_one' ? 'checked' : '' }}>
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
                            <h5 class="mb-0">Edit Questions</h5>
                            <div>
                                <span id="questionCount" class="badge bg-primary me-2">{{ $template->questions->count() }} Questions</span>
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
                                            <h6 class="mb-3" id="builderTitle">Add New Question</h6>
                                            
                                            <input type="hidden" id="editingQuestionId" value="">
                                            
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

                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="newIsMandatory">
                                                    <label class="form-check-label" for="newIsMandatory">Mark as mandatory</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="newEnableComments">
                                                    <label class="form-check-label" for="newEnableComments">Enable comments</label>
                                                </div>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-primary" id="saveQuestionBtn">
                                                    <i class="fas fa-save me-1"></i> Save Question
                                                </button>
                                                <button type="button" class="btn btn-secondary" id="cancelQuestionBtn">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Questions List -->
                                    <div id="questionsList" class="questions-list">
                                        @if($template->questions->count() > 0)
                                            @foreach($template->questions as $index => $question)
                                            <div class="card mb-2 question-card" data-id="{{ $question->id }}">
                                                <div class="card-body py-2">
                                                    <div class="d-flex gap-3 align-items-start">
                                                        <div class="question-number fw-bold text-primary">{{ $index + 1 }}</div>
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1"><strong>{{ $question->question_text }}</strong></p>
                                                            <small class="text-muted">Type: {{ ucfirst(str_replace('_', ' ', $question->type)) }}</small>
                                                            @if($question->is_mandatory)
                                                                <span class="badge bg-danger ms-2">Mandatory</span>
                                                            @endif
                                                            @if($question->enable_comments)
                                                                <span class="badge bg-info ms-2">Comments</span>
                                                            @endif
                                                            @if($question->answers->count() > 0)
                                                                <div class="mt-2">
                                                                    <small class="text-muted">Options:</small>
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        @foreach($question->answers as $answer)
                                                                        <span class="badge bg-light text-dark">{{ $answer->label }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm btn-outline-primary edit-question-btn" 
                                                                    data-id="{{ $question->id }}"
                                                                    data-type="{{ $question->type }}"
                                                                    data-text="{{ $question->question_text }}"
                                                                    data-mandatory="{{ $question->is_mandatory }}"
                                                                    data-comments="{{ $question->enable_comments }}"
                                                                    data-options="{{ $question->answers->pluck('label')->toJson() }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" data-id="{{ $question->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="text-center text-muted py-5">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>No questions added yet. Select a question type to get started.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
                        <i class="fas fa-arrow-left me-2"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                        <i class="fas fa-save me-2"></i> Update Template
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

.question-card {
    transition: all 0.2s ease;
}

.question-card:hover {
    background-color: #f8f9fa;
}

.question-number {
    min-width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    border-radius: 50%;
}
</style>

<script>
let currentStep = 1;
let questions = {!! json_encode($template->questions->map(function($q) {
    return [
        'id' => $q->id,
        'type' => $q->type,
        'text' => $q->question_text,
        'is_mandatory' => $q->is_mandatory,
        'enable_comments' => $q->enable_comments,
        'options' => $q->answers->pluck('label')->toArray()
    ];
})) !!};
let selectedType = null;
let questionOptions = [];
let editingQuestionId = null;

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('nextBtn').addEventListener('click', nextStep);
    document.getElementById('prevBtn').addEventListener('click', prevStep);
    document.getElementById('templateForm').addEventListener('submit', submitForm);

    // Question type selection
    document.querySelectorAll('.question-type-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            selectedType = btn.dataset.type;
            document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Reset editing mode
            editingQuestionId = null;
            document.getElementById('builderTitle').textContent = 'Add New Question';
            
            // Show question builder
            document.getElementById('questionBuilderForm').style.display = 'block';
            document.getElementById('newQuestionText').value = '';
            document.getElementById('newIsMandatory').checked = false;
            document.getElementById('newEnableComments').checked = false;
            questionOptions = [];
            
            // Show options section for types that need them
            const typesWithOptions = ['single', 'multiple', 'rating_scale', 'nps', 'star'];
            document.getElementById('optionsSection').style.display = typesWithOptions.includes(selectedType) ? 'block' : 'none';
            renderOptions();
            
            // Focus on question text
            document.getElementById('newQuestionText').focus();
        });
    });

    // Edit question button
    document.querySelectorAll('.edit-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            editingQuestionId = this.dataset.id;
            const question = questions.find(q => q.id == editingQuestionId);
            
            if (question) {
                // Set builder title
                document.getElementById('builderTitle').textContent = 'Edit Question';
                
                // Set question data
                selectedType = question.type;
                document.getElementById('newQuestionText').value = question.text;
                document.getElementById('newIsMandatory').checked = question.is_mandatory;
                document.getElementById('newEnableComments').checked = question.enable_comments;
                questionOptions = question.options || [];
                
                // Activate type button
                document.querySelectorAll('.question-type-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.type === selectedType);
                });
                
                // Show options section if needed
                const typesWithOptions = ['single', 'multiple', 'rating_scale', 'nps', 'star'];
                document.getElementById('optionsSection').style.display = typesWithOptions.includes(selectedType) ? 'block' : 'none';
                renderOptions();
                
                // Show builder
                document.getElementById('questionBuilderForm').style.display = 'block';
                document.getElementById('newQuestionText').focus();
            }
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
        const questionData = {
            id: editingQuestionId || Date.now(),
            type: selectedType,
            text: questionText,
            is_mandatory: document.getElementById('newIsMandatory').checked,
            enable_comments: document.getElementById('newEnableComments').checked,
            options: [...questionOptions]
        };

        // Update existing or add new
        if (editingQuestionId) {
            const index = questions.findIndex(q => q.id == editingQuestionId);
            if (index !== -1) {
                questions[index] = questionData;
            }
        } else {
            questions.push(questionData);
        }
        
        // Clear and hide builder
        document.getElementById('questionBuilderForm').style.display = 'none';
        document.getElementById('newQuestionText').value = '';
        questionOptions = [];
        editingQuestionId = null;
        document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
        
        // Render questions
        renderQuestions();
    });

    // Cancel question builder
    document.getElementById('cancelQuestionBtn').addEventListener('click', () => {
        document.getElementById('questionBuilderForm').style.display = 'none';
        document.getElementById('newQuestionText').value = '';
        questionOptions = [];
        editingQuestionId = null;
        document.querySelectorAll('.question-type-btn').forEach(b => b.classList.remove('active'));
    });

    // Remove question
    document.querySelectorAll('.remove-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete this question?')) return;
            
            const questionId = this.dataset.id;
            questions = questions.filter(q => q.id != questionId);
            renderQuestions();
        });
    });
});

function nextStep() {
    if (validateStep(currentStep)) {
        currentStep++;
        updateSteps();
    }
}

function prevStep() {
    currentStep--;
    updateSteps();
}

function updateSteps() {
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
    
    // Update question count on step 2
    if (currentStep === 2) {
        renderQuestions();
    }
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
    return true;
}

function renderOptions() {
    const optionsList = document.getElementById('optionsList');
    optionsList.innerHTML = questionOptions.map((option, index) => `
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">${index + 1}</span>
            <input type="text" class="form-control" value="${option}" readonly>
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
    
    questionsList.innerHTML = questions.map((q, index) => `
        <div class="card mb-2 question-card" data-id="${q.id}">
            <div class="card-body py-2">
                <div class="d-flex gap-3 align-items-start">
                    <div class="question-number fw-bold text-primary">${index + 1}</div>
                    <div class="flex-grow-1">
                        <p class="mb-1"><strong>${q.text}</strong></p>
                        <small class="text-muted">Type: ${q.type}</small>
                        ${q.is_mandatory ? '<span class="badge bg-danger ms-2">Mandatory</span>' : ''}
                        ${q.enable_comments ? '<span class="badge bg-info ms-2">Comments</span>' : ''}
                        ${q.options && q.options.length > 0 ? `
                            <div class="mt-2">
                                <small class="text-muted">Options:</small>
                                <div class="d-flex flex-wrap gap-1">
                                    ${q.options.map(opt => `<span class="badge bg-light text-dark">${opt}</span>`).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-question-btn" 
                                data-id="${q.id}"
                                data-type="${q.type}"
                                data-text="${q.text}"
                                data-mandatory="${q.is_mandatory}"
                                data-comments="${q.enable_comments}"
                                data-options='${JSON.stringify(q.options || [])}'>
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn" data-id="${q.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add event listeners to edit buttons
    document.querySelectorAll('.edit-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            editingQuestionId = this.dataset.id;
            const question = questions.find(q => q.id == editingQuestionId);
            
            if (question) {
                document.getElementById('builderTitle').textContent = 'Edit Question';
                selectedType = question.type;
                document.getElementById('newQuestionText').value = question.text;
                document.getElementById('newIsMandatory').checked = question.is_mandatory;
                document.getElementById('newEnableComments').checked = question.enable_comments;
                questionOptions = question.options || [];
                
                document.querySelectorAll('.question-type-btn').forEach(b => {
                    b.classList.toggle('active', b.dataset.type === selectedType);
                });
                
                const typesWithOptions = ['single', 'multiple', 'rating_scale', 'nps', 'star'];
                document.getElementById('optionsSection').style.display = typesWithOptions.includes(selectedType) ? 'block' : 'none';
                renderOptions();
                document.getElementById('questionBuilderForm').style.display = 'block';
            }
        });
    });
    
    // Add event listeners to remove buttons
    document.querySelectorAll('.remove-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Are you sure you want to delete this question?')) return;
            const questionId = this.dataset.id;
            questions = questions.filter(q => q.id != questionId);
            renderQuestions();
        });
    });
}

function submitForm(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value.trim();
    if (!name) {
        alert('Please enter a template name');
        currentStep = 1;
        updateSteps();
        document.getElementById('name').focus();
        return;
    }
    
    // Update the hidden input with current questions
    document.getElementById('questionsInput').value = JSON.stringify(questions);
    
    // Submit form
    document.getElementById('templateForm').submit();
}

// Also need to update the controller's update method to handle questions
// Initial update
updateSteps();
</script>
@endsection
