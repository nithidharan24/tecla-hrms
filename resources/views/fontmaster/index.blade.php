@extends('layouts.index')

@section('content')
<div class="container py-5">
    <div class="card shadow border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-font me-2"></i> Font Settings (Google Fonts)</h4>
        </div>
        <div class="card-body p-4">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row align-items-center">
                {{-- Left Side - Font Selection --}}
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <form method="POST" action="{{ route('fontmaster.update') }}" class="p-3 border rounded-3 bg-light">
                        @csrf
                        <div class="mb-3">
                            <label for="font_family" class="form-label fw-semibold">Select Google Font</label>
                            <select name="font_family" id="font_family" class="form-select shadow-sm">
                                @foreach($availableFonts as $font)
                                    <option value="{{ $font }}" {{ $selectedFont == $font ? 'selected' : '' }}>
                                        {{ $font }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa fa-save me-2"></i>Save Font
                        </button>
                    </form>
                </div>

                {{-- Right Side - Font Preview --}}
                <div class="col-lg-7">
                    <div class="p-4 border rounded-3 bg-white shadow-sm">
                        <h5 class="fw-semibold mb-3 d-flex align-items-center">
                            <i class="fa fa-eye me-2 text-primary"></i> Live Font Preview
                        </h5>

                        <p id="fontPreview" class="border rounded-3 p-3 mb-0"
                           style="font-size: 22px; line-height: 1.5; min-height: 120px; transition: all 0.3s ease;">
                            The quick brown fox jumps over the lazy dog.
                        </p>

                        <div class="text-muted mt-2" style="font-size: 14px;">
                            <i class="fa fa-info-circle me-1"></i>
                            Select a font from the left to preview it instantly.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert --}}
@if(session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Font Updated!',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif

{{-- Live Preview Script --}}
<script>
const fontSelect = document.getElementById('font_family');
const preview = document.getElementById('fontPreview');

function updatePreviewFont(font) {
    const existingLink = document.getElementById('googleFontLink');
    if (existingLink) existingLink.remove();

    const fontLink = document.createElement('link');
    fontLink.id = 'googleFontLink';
    fontLink.rel = 'stylesheet';
    fontLink.href = `https://fonts.googleapis.com/css2?family=${font.replace(/ /g, '+')}:wght@300;400;500;600;700&display=swap`;
    document.head.appendChild(fontLink);

    preview.style.fontFamily = `'${font}', sans-serif`;
}

updatePreviewFont(fontSelect.value);

fontSelect.addEventListener('change', function() {
    updatePreviewFont(this.value);
});
</script>
@endsection
