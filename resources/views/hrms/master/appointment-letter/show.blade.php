@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">View Appointment Letter Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('appointment-letter.index') }}">Appointment Letters</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('appointment-letter.edit', $appointmentLetter->id) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                <a href="{{ route('appointment-letter.preview', $appointmentLetter->id) }}" class="btn btn-info" target="_blank"><i class="fa fa-eye"></i> Preview</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Template Name:</strong></label>
                        <p>{{ $appointmentLetter->name }}</p>
                    </div>
                    <div class="form-group">
                        <label><strong>Template Content:</strong></label>
                        <div style="background: #f4f4f4; padding: 15px; border-radius: 5px; max-height: 500px; overflow-y: auto;">
                            <pre><code>{{ $appointmentLetter->content }}</code></pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><strong>Created At:</strong></label>
                        <p>{{ $appointmentLetter->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="form-group">
                        <label><strong>Last Updated:</strong></label>
                        <p>{{ $appointmentLetter->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
