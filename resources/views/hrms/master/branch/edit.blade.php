@extends('layouts.index')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Edit Branch</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">Branches</a></li>
                        <li class="breadcrumb-item active">Edit </li>
                    </ul>
                </div>
                <div class="col-auto float-end ms-auto">
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to Branches
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fa fa-edit"></i> Edit Branch Information
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('branches.update', $branch->id) }}" method="POST" id="editBranchForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Branch Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               name="name" value="{{ old('name', $branch->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  name="address" rows="3" required>{{ old('address', $branch->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               name="phone" value="{{ old('phone', $branch->phone) }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    
                                    
                                   
                                    <div class="row">
                                       <div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Opening Time <span class="text-danger">*</span></label>
            <input type="time" class="form-control @error('opening_time') is-invalid @enderror" 
                   name="opening_time" value="{{ old('opening_time', \Carbon\Carbon::parse($branch->opening_time)->format('H:i')) }}" required>
            @error('opening_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Closing Time <span class="text-danger">*</span></label>
            <input type="time" class="form-control @error('closing_time') is-invalid @enderror" 
                   name="closing_time" value="{{ old('closing_time', \Carbon\Carbon::parse($branch->closing_time)->format('H:i')) }}" required>
            @error('closing_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
                                    </div>
                                    
                                  
                                </div>
                            </div>
                            
                            <div class="text-end">
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Branch
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('editBranchForm').addEventListener('submit', function(e) {
            const openingTime = document.querySelector('input[name="opening_time"]').value;
            const closingTime = document.querySelector('input[name="closing_time"]').value;
            
            if (openingTime >= closingTime) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Time Range',
                    text: 'Closing time must be after opening time.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
@endsection