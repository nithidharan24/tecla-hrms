@extends('layouts.index')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Create New Customization</h3>
                    <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
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

                    <form action="{{ route('customize-site.store') }}" method="POST" enctype="multipart/form-data" id="customizationForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="key" class="form-label">Key <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('key') is-invalid @enderror" 
                                           id="key" 
                                           name="key" 
                                           value="{{ old('key') }}" 
                                           placeholder="e.g., main_logo, site_title, etc."
                                           required>
                                    <div class="form-text">Unique identifier for this customization (use underscore for spaces)</div>
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
                                        <option value="">Select Type</option>
                                        <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
                                        <option value="image" {{ old('type') === 'image' ? 'selected' : '' }}>Image</option>
                                        <option value="json" {{ old('type') === 'json' ? 'selected' : '' }}>JSON</option>
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
                                      rows="3" 
                                      placeholder="Brief description of this customization">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Text/JSON Value Input -->
                        <div class="mb-3" id="valueInput" style="display: none;">
                            <label for="value" class="form-label">Value</label>
                            <textarea class="form-control @error('value') is-invalid @enderror" 
                                      id="value" 
                                      name="value" 
                                      rows="4" 
                                      placeholder="Enter the value...">{{ old('value') }}</textarea>
                            <div class="form-text" id="valueHelp"></div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload Input -->
                        <div class="mb-3" id="imageInput" style="display: none;">
                            <label for="logo_file" class="form-label">Upload Image</label>
                            <input type="file" 
                                   class="form-control @error('logo_file') is-invalid @enderror" 
                                   id="logo_file" 
                                   name="logo_file" 
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB</div>
                            @error('logo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <label class="form-label">Preview:</label>
                                <div class="border rounded p-3 text-center">
                                    <img id="previewImg" src="/placeholder.svg" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <div class="form-text">Uncheck to create as inactive</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Templates -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Quick Templates</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('main_logo', 'image', 'Main site logo')">
                                            Main Logo
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('site_favicon', 'image', 'Site favicon')">
                                            Favicon
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('site_title', 'text', 'Main site title')">
                                            Site Title
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('contact_email', 'text', 'Contact email address')">
                                            Contact Email
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('social_links', 'json', 'Social media links')">
                                            Social Links
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" 
                                                onclick="fillTemplate('theme_colors', 'json', 'Theme color configuration')">
                                            Theme Colors
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customize-site.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Customization
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
    const valueField = document.getElementById('value');
    const logoField = document.getElementById('logo_file');

    // Hide all inputs first
    valueInput.style.display = 'none';
    imageInput.style.display = 'none';
    
    // Remove required attributes
    valueField.removeAttribute('required');
    logoField.removeAttribute('required');

    if (type === 'text') {
        valueInput.style.display = 'block';
        valueHelp.textContent = 'Enter plain text value';
        valueField.setAttribute('required', 'required');
    } else if (type === 'json') {
        valueInput.style.display = 'block';
        valueHelp.textContent = 'Enter valid JSON format, e.g., {"key": "value"}';
        valueField.setAttribute('required', 'required');
    } else if (type === 'image') {
        imageInput.style.display = 'block';
        logoField.setAttribute('required', 'required');
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

function fillTemplate(key, type, description) {
    document.getElementById('key').value = key;
    document.getElementById('type').value = type;
    document.getElementById('description').value = description;
    
    // Trigger the type change to show appropriate fields
    toggleInputFields();
    
    // Fill sample values for JSON types
    if (type === 'json') {
        const valueField = document.getElementById('value');
        if (key === 'social_links') {
            valueField.value = JSON.stringify({
                "facebook": "https://facebook.com/yourpage",
                "twitter": "https://twitter.com/yourhandle",
                "instagram": "https://instagram.com/yourprofile"
            }, null, 2);
        } else if (key === 'theme_colors') {
            valueField.value = JSON.stringify({
                "primary": "#007bff",
                "secondary": "#6c757d",
                "success": "#28a745",
                "danger": "#dc3545"
            }, null, 2);
        }
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