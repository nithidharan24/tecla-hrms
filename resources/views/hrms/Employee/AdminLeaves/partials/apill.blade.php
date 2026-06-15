@php
$cls   = $val === 'approved' ? 'al-apill-approved' : ($val === 'declined' ? 'al-apill-declined' : ($val === 'pending' ? 'al-apill-pending' : 'al-apill-null'));
$icon  = $val === 'approved' ? '✓' : ($val === 'declined' ? '✗' : ($val === 'pending' ? '⏳' : '–'));
$label = ucfirst($val ?? 'N/A');
@endphp
<span class="al-apill {{ $cls }}">
    {{ $icon }} {{ $label }}
    @if(!empty($name))<span class="al-apill-name">{{ $name }}</span>@endif
</span>
