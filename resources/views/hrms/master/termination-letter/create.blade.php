@extends('layouts.index')
@section('content')
<div class="content container-fluid" style="padding-top: 20px;">
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12">
                <div class="page-header">
                    <h2 class="pageheader-title">Add Termination Letter Template</h2>
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/" class="breadcrumb-link">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('termination-letter-templates.index') }}" class="breadcrumb-link">Templates List</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Template</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-xl-12">
                <div class="card shadow-sm">
                    <h4 class="card-header">Template Details</h4>
                    <div class="card-body">
                        <form method="POST" action="{{ route('termination-letter-templates.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name">Template Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject">Email Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="content">Template Content (HTML/Blade) <span class="text-danger">*</span></label>
                                    @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'termination-letter.blade.php', 'buttonText' => 'Load Default'])
                                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" required>{{ old('content') }}</textarea>
                                    <small class="form-text text-muted">Use `{{ '{' }}{'{'}} $employee->firstname {{ '}' }}{{'}'}}`, `{{ '{' }}{'{'}} $termination->reason {{ '}' }}{{'}'}}` etc. for dynamic data. This content will be used for both email and PDF.</small>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mt-4">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">Save Template</button>
                                    <a href="{{ route('termination-letter-templates.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
