<div class="card border">
    <div class="card-body">
        <form action="{{ route('email-config-setting-update') }}" method="POST">
            @csrf
            @method('POST')

            <div class="row">
                <!-- Email Address -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>From Email Address *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email', @$emailSettings->email) }}" 
                               placeholder="noreply@yourdomain.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">This email will be used as the sender for all outgoing emails.</small>
                    </div>
                </div>

                <!-- SMTP Host -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>SMTP Host *</label>
                        <input type="text" class="form-control @error('host') is-invalid @enderror" 
                               name="host" value="{{ old('host', @$emailSettings->host) }}" 
                               placeholder="smtp.yourdomain.com">
                        @error('host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- SMTP Username -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>SMTP Username *</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               name="username" value="{{ old('username', @$emailSettings->username) }}" 
                               placeholder="noreply@yourdomain.com">
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- SMTP Password -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>SMTP Password *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" value="{{ old('password', @$emailSettings->password) }}" 
                               placeholder="Your SMTP password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- SMTP Port -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>SMTP Port *</label>
                        <input type="text" class="form-control @error('port') is-invalid @enderror" 
                               name="port" value="{{ old('port', @$emailSettings->port) }}" 
                               placeholder="587">
                        @error('port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Encryption -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>Encryption *</label>
                        <select class="form-control @error('encryption') is-invalid @enderror" name="encryption">
                            <option value="tls" {{ old('encryption', @$emailSettings->encryption) == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('encryption', @$emailSettings->encryption) == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ old('encryption', @$emailSettings->encryption) == '' ? 'selected' : '' }}>None</option>
                        </select>
                        @error('encryption') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- Test Email Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Email Testing</h6>
                        <p class="mb-2">After saving your configuration, you can test if emails are working properly.</p>
                        <small>All system emails will be sent from: <strong>{{ @$emailSettings->email ?: 'Not configured' }}</strong></small>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary">Update Email Configuration</button>
            </div>
        </form>
    </div>
</div>