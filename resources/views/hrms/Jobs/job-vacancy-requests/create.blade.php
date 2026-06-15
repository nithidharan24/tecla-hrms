@extends('layouts.index')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Create Job Vacancy Request</h3>
                </div>
                <div class="card-body p-4">
                    @include('hrms.Jobs.job-vacancy-requests.form', [
                        'action' => route('job-vacancy-requests.store'),
                        'method' => 'POST',
                        'buttonText' => 'Submit',
                        'jobRequest' => null
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
