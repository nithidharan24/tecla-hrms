@extends('layouts.index')
@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Branch Details</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Branches</a></li>
                        <li class="breadcrumb-item active">{{ $branch->name }}</li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-primary me-2">
                        <i class="fa fa-edit"></i> Edit Branch
                    </a>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Branches
                    </a>
                </div>
            </div>
        </div>

        <!-- Branch Details -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-building"></i> {{ $branch->name }}
                            <span class="badge bg-{{ $branch->status ? 'success' : 'danger' }} ms-2">
                                {{ $branch->status ? 'Active' : 'Inactive' }}
                            </span>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Branch Name:</th>
                                        <td>{{ $branch->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td>{{ $branch->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ $branch->phone }}</td>
                                    </tr>
                                    @if(isset($branch->email))
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $branch->email }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Opening Time:</th>
                                        <td>{{ \Carbon\Carbon::parse($branch->opening_time)->format('h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Closing Time:</th>
                                        <td>{{ \Carbon\Carbon::parse($branch->closing_time)->format('h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $branch->status ? 'success' : 'danger' }}">
                                                {{ $branch->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created:</th>
                                        <td>{{ $branch->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
