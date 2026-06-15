@extends('layouts.index')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Offer Letter Template</h3>
                    <div class="card-tools">
                        <a href="{{ route('offer-letter.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <form action="{{ route('offer-letter.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Template Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">Email Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" value="{{ old('subject', 'Job Offer Letter') }}" required>
                                    @error('subject')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Set as Active Template
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content">Letter Content <span class="text-danger">*</span></label>
                            @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'offer-letter.blade.php', 'buttonText' => 'Load Default'])
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Available Placeholders:</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul>
                                        <li><code>@{{candidate_name@}}</code> - Full name</li>
                                        <li><code>@{{first_name@}}</code> - First name</li>
                                        <li><code>@{{last_name@}}</code> - Last name</li>
                                        <li><code>@{{email@}}</code> - Email address</li>
                                        <li><code>@{{phone@}}</code> - Phone number</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                        <li><code>@{{position@}}</code> - Position applied</li>
                                        <li><code>@{{experience@}}</code> - Years of experience</li>
                                        <li><code>@{{salary@}}</code> - Expected salary</li>
                                        <li><code>@{{date@}}</code> - Current date</li>
                                        <li><code>@{{company_name@}}</code> - Company name</li>
                                        <li><code>@{{ $offerSignatureDataUri }}</code> - Offer letter signature image</li>
                                    </ul>
                                </div>
                            </div>
                            <p><small class="text-muted">Note: Replace @ with { when using in your template content</small></p>
                            <div class="mt-2">
                                <strong>Signature block for this template:</strong>
                                <pre class="mb-0 mt-2 p-2 bg-light border rounded" style="white-space: pre-wrap;"><code>@verbatim@if(!empty($offerSignatureDataUri))
    &lt;div class="authorized-signature"&gt;
        &lt;img src="{{ $offerSignatureDataUri }}" alt="Authorized Signature"&gt;
    &lt;/div&gt;
@endif@endverbatim</code></pre>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Template
                        </button>
                        <a href="{{ route('offer-letter.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize rich text editor if available
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('content');
    }
});
</script>
@endsection
