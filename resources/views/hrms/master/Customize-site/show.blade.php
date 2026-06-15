@extends('layouts.index')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">View Customization</h3>
                    <div>
                        <a href="{{ route('customize-site.edit', $customization->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="200">Key</th>
                                    <td><code>{{ $customization->key }}</code></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge bg-{{ $customization->type === 'image' ? 'info' : ($customization->type === 'json' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($customization->type) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $customization->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-{{ $customization->is_active ? 'success' : 'danger' }}">
                                            {{ $customization->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ \Carbon\Carbon::parse($customization->created_at)->format('M d, Y H:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ \Carbon\Carbon::parse($customization->updated_at)->format('M d, Y H:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            @if($customization->type === 'image' && $customization->value)
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Image Preview</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="{{ asset('storage/' . $customization->value) }}" 
                                             alt="Customization Image" 
                                             class="img-fluid rounded"
                                             style="max-height: 300px;">
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $customization->value) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-external-link-alt"></i> View Full Size
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Value Content</h6>
                                </div>
                                <div class="card-body">
                                    @if($customization->type === 'json')
                                        <pre class="bg-light p-3 rounded"><code>{{ json_encode(json_decode($customization->value), JSON_PRETTY_PRINT) }}</code></pre>
                                    @elseif($customization->type === 'image')
                                        <p class="text-muted">Image file path: <code>{{ $customization->value }}</code></p>
                                    @else
                                        <div class="bg-light p-3 rounded">
                                            {{ $customization->value }}
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