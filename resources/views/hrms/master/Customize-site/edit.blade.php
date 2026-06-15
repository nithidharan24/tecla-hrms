@extends('layouts.index')

@section('content')


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit Customization</h3>
                    <div>
                        <a href="{{ route('customize-site.show', $customization->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('customize-site.update', $customization->id) }}" method="POST" enctype="multipart/form-data" id="customizationForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('key') is-invalid @enderror" 
                                           id="key" 
                                           name="key" 
                                           value="{{ old('key', $customization->key) }}" 
                                           required>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" 
                                            name="type" 
                                            onchange="toggleInputFields()" 
                                            required>
                                        <option value="text" {{ old('type', $customization->type) === 'text' ? 'selected' : '' }}>Text</option>
                                        <option value="image" {{ old('type', $customization->type) === 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="json" {{ old('type', $customization->type) === 'json' ? 'selected' : '' }}>JSON</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $customization->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Image Display -->
                        @if($customization->type === 'image' && $customization->value)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="border rounded p-3 text-center">
                                <img src="{{ asset('storage/' . $customization->value) }}" 
                                     alt="Current Image" 
                                     style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                        @endif

                        <!-- Text/JSON Value Input -->
                        <div class="mb-3" id="valueInput">
                            <label for="value" class="form-label">Value</label>
                            <textarea class="form-control @error('value') is-invalid @enderror" 
                                      id="value" 
                                      name="value" 
                                      rows="4">{{ old('value', $customization->value) }}</textarea>
                            <div class="form-text" id="valueHelp"></div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload Input -->
                        <div class="mb-3" id="imageInput">
                            <label for="logo_file" class="form-label">
                                {{ $customization->type === 'image' ? 'Replace Image' : 'Upload Image' }}
                            </label>
                            <input type="file" 
                                   class="form-control @error('logo_file') is-invalid @enderror" 
                                   id="logo_file" 
                                   name="logo_file" 
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <div class="form-text">
                                {{ $customization->type === 'image' ? 'Leave empty to keep current image. ' : '' }}
                                Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB
                            </div>
                            @error('logo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- New Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <label class="form-label">New Image Preview:</label>
                                <div class="border rounded p-3 text-center">
                                    <img id="previewImg" src="/placeholder.svg" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', $customization->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Customization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleInputFields() {
    const type = document.getElementById('type').value;
    const valueInput = document.getElementById('valueInput');
    const imageInput = document.getElementById('imageInput');
    const valueHelp = document.getElementById('valueHelp');

    if (type === 'text') {
        valueInput.style.display = 'block';
        imageInput.style.display = 'none';
        valueHelp.textContent = 'Enter plain text value';
    } else if (type === 'json') {
        valueInput.style.display = 'block';
        imageInput.style.display = 'none';
        valueHelp.textContent = 'Enter valid JSON format, e.g., {"key": "value"}';
    } else if (type === 'image') {
        valueInput.style.display = 'none';
        imageInput.style.display = 'block';
    }
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleInputFields();
});

// Form validation
document.getElementById('customizationForm').addEventListener('submit', function(e) {
    const type = document.getElementById('type').value;
    const value = document.getElementById('value').value;
    
    if (type === 'json' && value) {
        try {
            JSON.parse(value);
        } catch (error) {
            e.preventDefault();
            alert('Please enter valid JSON format');
            return false;
        }
    }
});
</script>


@endsection