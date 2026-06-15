    <div class="card border">
        <div class="card-body">
            <form action="{{ route('logo-setting-update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                
              <!-- Logo Upload Section -->
            <div class="form-group mb-3">
                <label>Logo</label><br>
                <img 
                    id="logoPreview" 
                    src="{{ @$logoSetting->logo ? asset($logoSetting->logo) : asset('assets/admin/img/temp/default.jpg') }}" 
                    width="150px" 
                    alt="Logo Preview">
                <br>
                <input type="file" class="form-control mt-3" name="logo" onchange="previewImage(event, 'logoPreview')">
                <input type="hidden" name="old_logo" value="{{ @$logoSetting->logo }}">
                 <small id="fileName" class="text-muted d-block mt-2">
        Current file: 
        {{ @$logoSetting->logo ? basename($logoSetting->logo) : 'No file uploaded' }}
    </small>
            </div>

       

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
<script>
    // Function to preview the selected image
    function previewImage(event, previewId) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById(previewId);
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
