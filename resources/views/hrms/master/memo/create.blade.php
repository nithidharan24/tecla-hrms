@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create Memo Template</h3>
                    <a href="{{ route('memo.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <form action="{{ route('memo.store') }}" method="POST" id="memo-form">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Template Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Disciplinary Memo" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Official Memo Regarding Work Conduct" required>
                                    @error('subject')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content">Template Content <span class="text-danger">*</span></label>
                            <div class="d-flex justify-content-end mb-2">
                                @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'memo-letter.blade.php', 'targetId' => 'content', 'buttonText' => 'Load Default'])
                            </div>
                            <textarea class="form-control memo-code-editor @error('content') is-invalid @enderror" id="content" name="content" rows="18" required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info mt-4">
                            <h5><i class="icon fas fa-info"></i> Available Placeholders:</h5>
                            <p class="mb-1"><code>@{{ $employee->firstname }}</code>, <code>@{{ $employee->lastname }}</code>, <code>@{{ $employee->employeeid }}</code>, <code>@{{ $employee->designation_name }}</code>, <code>@{{ $employee->department_name }}</code>, <code>@{{ $employee->branch_name }}</code>, <code>@{{ $memo->subject }}</code>, <code>@{{ $offerSignatureDataUri }}</code></p>
                            <p class="mb-0"><small class="text-muted">Use <code>@{{ ... }}</code> to keep template placeholders safe in the editor. They are converted before rendering.</small></p>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="{{ route('memo.index') }}" class="btn btn-secondary">Cancel</a>
                        <div>
                            <button type="button" class="btn btn-info me-2" onclick="previewMemoDraft()">
                                <i class="fas fa-file-pdf"></i> Preview PDF
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Template
                            </button>
                        </div>
                    </div>
                </form>

                <form id="memo-preview-form" action="{{ route('memo.preview-draft') }}" method="POST" target="_blank" style="display:none;">
                    @csrf
                    <input type="hidden" name="name" id="preview-name">
                    <input type="hidden" name="subject" id="preview-subject">
                    <input type="hidden" name="content" id="preview-content">
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .memo-code-editor {
        min-height: 460px;
        font-family: Consolas, "Courier New", monospace;
        font-size: 13px;
        line-height: 1.45;
        white-space: pre;
        tab-size: 4;
        resize: vertical;
    }
</style>
<script>
    function previewMemoDraft() {
        document.getElementById('preview-name').value = document.getElementById('name').value;
        document.getElementById('preview-subject').value = document.getElementById('subject').value;
        document.getElementById('preview-content').value = document.getElementById('content').value;
        document.getElementById('memo-preview-form').submit();
    }
</script>
@endsection
