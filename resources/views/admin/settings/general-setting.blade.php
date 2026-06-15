<div class="card border">
    <div class="card-body">
        <form action="{{ route('generale-setting-update') }}" method="POST">
            @csrf
            @method('POST')

            <!-- Site Name -->
            <div class="form-group mb-3">
                <label>Site Name</label>
                <input type="text" class="form-control @error('site_name') is-invalid @enderror" name="site_name" value="{{ old('site_name', @$generalSettings->site_name) }}">
                @error('site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Contact Email -->
            <div class="form-group mb-3">
                <label>Contact Email</label>
                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" name="contact_email" value="{{ old('contact_email', @$generalSettings->contact_email) }}">
                @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Contact Phone -->
            <div class="form-group mb-3">
                <label>Contact Phone</label>
                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" name="contact_phone" value="{{ old('contact_phone', @$generalSettings->contact_phone) }}">
                @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

        <!-- Contact Address -->
<div class="form-group mb-3">
    <label>Contact Address</label>
    <textarea class="form-control @error('contact_address') is-invalid @enderror" name="contact_address" rows="3">{{ old('contact_address', @$generalSettings->contact_address) }}</textarea>
    @error('contact_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>



            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
