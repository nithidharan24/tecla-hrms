@php
$cls  = $val === 'approved' ? 'lv-apill-approved' : ($val === 'declined' ? 'lv-apill-declined' : ($val === 'pending' ? 'lv-apill-pending' : 'lv-apill-null'));
$icon = $val === 'approved' ? '✓' : ($val === 'declined' ? '✗' : ($val === 'pending' ? '⏳' : '–'));
$label = ucfirst($val ?? 'N/A');
@endphp
<span class="lv-apill {{ $cls }}">
    {{ $icon }} {{ $label }}
    @if(!empty($name))<span class="lv-apill-name">{{ $name }}</span>@endif
</span>
