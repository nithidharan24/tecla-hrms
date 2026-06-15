@php
    $targetId = $targetId ?? 'content';
    $buttonText = $buttonText ?? 'Load Default';
    $templateFile = $templateFile ?? null;
    $templatePath = $templateFile ? resource_path('views/hrms/master/letters/templates/' . $templateFile) : null;
    $templateContent = ($templatePath && \Illuminate\Support\Facades\File::exists($templatePath))
        ? \Illuminate\Support\Facades\File::get($templatePath)
        : '';
@endphp

<button
    type="button"
    class="btn btn-sm btn-outline-primary float-end"
    data-target-id="{{ $targetId }}"
    data-template-base64="{{ base64_encode($templateContent) }}"
    onclick="hrmsLoadDefaultTemplate(this)"
    {{ $templateContent === '' ? 'disabled' : '' }}>
    <i class="fa fa-file-import"></i> {{ $buttonText }}
</button>

@once
    <script>
        function hrmsDecodeTemplate(base64Value) {
            var binary = atob(base64Value || '');
            var bytes = new Uint8Array(binary.length);

            for (var index = 0; index < binary.length; index++) {
                bytes[index] = binary.charCodeAt(index);
            }

            return new TextDecoder('utf-8').decode(bytes);
        }

        function hrmsLoadDefaultTemplate(button) {
            var targetId = button.getAttribute('data-target-id') || 'content';
            var template = hrmsDecodeTemplate(button.getAttribute('data-template-base64'));
            var textarea = document.getElementById(targetId);

            if (!textarea) {
                alert('Template editor not found.');
                return;
            }

            if (window.CKEDITOR && CKEDITOR.instances[targetId]) {
                CKEDITOR.instances[targetId].setData(template);
            } else {
                textarea.value = template;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                textarea.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Default template loaded',
                    timer: 1300,
                    showConfirmButton: false
                });
            } else {
                alert('Default template loaded successfully.');
            }
        }
    </script>
@endonce
