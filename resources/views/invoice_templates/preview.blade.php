@extends('layouts.index')

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Preview Invoice Template</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoice-template.index') }}">Invoice Templates</a></li>
                    <li class="breadcrumb-item active">Preview</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="{{ route('invoice-template.edit', $invoiceTemplate->id) }}" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Template Name:</strong></label>
                        <p>{{ $invoiceTemplate->name }}</p>
                    </div>
                    <div class="form-group">
                        <label><strong>Created At:</strong></label>
                        <p>{{ $invoiceTemplate->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="form-group">
                        <label><strong>Last Updated:</strong></label>
                        <p>{{ $invoiceTemplate->updated_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection