@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Add Question</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                    <li class="breadcrumb-item active">Add Question</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('Question.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Question <span class="text-danger">*</span></label>
                            <textarea name="question" class="form-control" rows="3" placeholder="Enter your question here..." required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="Technical" {{ old('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                                <option value="Behavioral" {{ old('category') == 'Behavioral' ? 'selected' : '' }}>Behavioral</option>
                                <option value="Logical" {{ old('category') == 'Logical' ? 'selected' : '' }}>Logical</option>
                                <option value="General" {{ old('category') == 'General' ? 'selected' : '' }}>General</option>
                            </select>
                            @error('category')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Option A <span class="text-danger">*</span></label>
                            <input type="text" name="option_a" class="form-control" value="{{ old('option_a') }}" required>
                            @error('option_a')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Option B <span class="text-danger">*</span></label>
                            <input type="text" name="option_b" class="form-control" value="{{ old('option_b') }}" required>
                            @error('option_b')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Option C <span class="text-danger">*</span></label>
                            <input type="text" name="option_c" class="form-control" value="{{ old('option_c') }}" required>
                            @error('option_c')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Option D <span class="text-danger">*</span></label>
                            <input type="text" name="option_d" class="form-control" value="{{ old('option_d') }}" required>
                            @error('option_d')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correct Answer <span class="text-danger">*</span></label>
                            <select name="correct_answer" class="form-control" required>
                                <option value="A" {{ old('correct_answer') == 'A' ? 'selected' : '' }}>Option A</option>
                                <option value="B" {{ old('correct_answer') == 'B' ? 'selected' : '' }}>Option B</option>
                                <option value="C" {{ old('correct_answer') == 'C' ? 'selected' : '' }}>Option C</option>
                                <option value="D" {{ old('correct_answer') == 'D' ? 'selected' : '' }}>Option D</option>
                            </select>
                            @error('correct_answer')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Code Snippets</label>
                            <textarea name="code_snippets" class="form-control" rows="3" placeholder="Optional: Add code snippets here...">{{ old('code_snippets') }}</textarea>
                            @error('code_snippets')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Answer Explanation</label>
                            <textarea name="answer_explanation" class="form-control" rows="3" placeholder="Optional: Explain the answer...">{{ old('answer_explanation') }}</textarea>
                            @error('answer_explanation')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Video Link</label>
                            <input type="url" name="video_link" class="form-control" value="{{ old('video_link') }}" placeholder="Optional: YouTube or Vimeo link">
                            @error('video_link')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Add Image To Question</label>
                            <input type="file" name="question_image" class="form-control">
                            @error('question_image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="submit-section">
                    <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection