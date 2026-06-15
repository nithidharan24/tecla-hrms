@extends('layouts.index')

@section('content')
<div class="content container-fluid mt-3">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>Offer Approval Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><strong>Candidate Information</strong></h6>
                            <p><strong>Name:</strong> {{ $candidate->first_name }} {{ $candidate->last_name }}</p>
                            <p><strong>Email:</strong> {{ $candidate->email }}</p>
                            <p><strong>Position:</strong> {{ $candidate->position_applied }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Offer Status</strong></h6>
                            @if($offer)
                                <span class="badge bg-success">{{ ucwords(str_replace('_', ' ', $offer->offer_status)) }}</span>
                            @else
                                <div class="alert alert-info mb-0">No offer created yet</div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('recruitment.index', ['tab' => 'add-resume']) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
