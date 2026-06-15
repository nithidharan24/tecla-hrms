@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Edit Hike Letter Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hike-letter.index') }}">Hike Letters</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('hike-letter.update', $hikeLetter->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $hikeLetter->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content">Template Content (HTML/Blade) <span class="text-danger">*</span></label>
                            @include('hrms.master.letters.partials.load-default-template', ['templateFile' => 'hike-letter.blade.php', 'buttonText' => 'Load Default'])
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="25" required>{{ old('content', $hikeLetter->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="submit-section">
                            <button type="submit" class="btn btn-primary submit-btn">Update</button>
                            <a href="{{ route('hike-letter.index') }}" class="btn btn-secondary submit-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
