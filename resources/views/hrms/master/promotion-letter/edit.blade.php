@extends('layouts.index')

@section('content')
<div class="content container-fluid">
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Edit Promotion Letter Template</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('promotion-letter.index') }}">Promotion Letters</a></li>
                <li class="breadcrumb-item">Edit</li>
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('promotion-letter.update', $promotionLetter->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Template Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $promotionLetter->name) }}" required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="content">Template Content (HTML/Blade) <span class="text-danger">*</span></label>
                @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'promotion-letter.blade.php', 'buttonText' => 'Load Default'])
                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="15" required>{{ old('content', $promotionLetter->content) }}</textarea>
                @error('content')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <small class="form-text text-muted">
                    You can use Blade syntax here. Available variables: `@{{ $employee }}`, `@{{ $promotion }}`, `@{{ $oldDesignationName }}`, `@{{ $newDesignationName }}`.
                    Example: `Congratulations, @{{ $employee->firstname }} @{{ $employee->lastname }}! You are promoted to @{{ $newDesignationName }}.`
                </small>
            </div>
            <div class="submit-section">
                <button type="submit" class="btn btn-primary submit-btn">Update</button>
                
                  <a href="{{ route('promotion-letter.index') }}" class="btn btn-secondary submit-btn" >Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
@endsection
