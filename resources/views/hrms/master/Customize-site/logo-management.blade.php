@extends('layouts.index')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Logo Management</h3>
                    <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Customizations
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Main Logo Section -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Main Site Logo</h5>
                                </div>
                                <div class="card-body text-center">
                                    @php
                                        $mainLogo = $logos->where('key', 'main_logo')->first();
                                    @endphp
                                    @if($mainLogo && $mainLogo->value)
                                        <img src="{{ asset('storage/' . $mainLogo->value) }}" 
                                             alt="Main Logo" 
                                             style="max-height: 100px; max-width: 300px; object-fit: contain;"
                                             class="mb-3">
                                    @else
                                        <div class="text-muted mb-3">
                                            <i class="fas fa-image fa-3x"></i>
                                            <p>No logo uploaded</p>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('customize-site.logo.main') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="file" class="form-control" name="main_logo" accept="image/*" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload"></i> Update Main Logo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Add Currency Logo</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('customize-site.logo.currency') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="currency_code" class="form-label">Currency Code</label>
                                            <input type="text" class="form-control" id="currency_code" name="currency_code" 
                                                   placeholder="USD" maxlength="10" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="currency_logo" class="form-label">Currency Logo</label>
                                            <input type="file" class="form-control" id="currency_logo" name="currency_logo" 
                                                   accept="image/*" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Add Currency Logo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Currency Logos Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Currency Logos</h5>
                                </div>
                                <div class="card-body">
                                    @if($currencies->count() > 0)
                                        <div class="row">
                                            @foreach($currencies as $currency)
                                                @php
                                                    $currencyCode = strtoupper(str_replace('currency_logo_', '', $currency->key));
                                                @endphp
                                                <div class="col-md-3 mb-4">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <img src="{{ asset('storage/' . $currency->value) }}" 
                                                                 alt="{{ $currencyCode }} Logo" 
                                                                 style="max-height: 60px; max-width: 60px; object-fit: contain;"
                                                                 class="mb-2">
                                                            <h6 class="mb-2">{{ $currencyCode }}</h6>
                                                            <p class="small text-muted mb-3">{{ $currency->description }}</p>
                                                            <form action="{{ route('customize-site.logo.currency.delete', strtolower($currencyCode)) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this currency logo?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-coins fa-3x mb-3"></i>
                                            <p>No currency logos added yet. Use the form above to add your first currency logo.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection