@php
    $canChange = $canChange ?? false;
    $colorMap  = ['approved' => 'success', 'declined' => 'danger', 'pending' => 'warning'];
    $color     = $colorMap[$value ?? 'pending'] ?? 'warning';
    $label     = ucfirst($value ?? 'Pending');
@endphp

@if($canChange)
<div class="dropdown action-label">
    <button class="btn btn-white btn-sm btn-rounded dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fa-regular fa-circle-dot text-{{ $color }}"></i> {{ $label }}
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        @foreach(['pending' => 'warning', 'approved' => 'success', 'declined' => 'danger'] as $s => $c)
        <form action="{{ $url }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="{{ $s }}">
            <input type="hidden" name="stage"  value="{{ $stage }}">
            <button type="submit" class="dropdown-item">
                <i class="fa-regular fa-circle-dot text-{{ $c }}"></i> {{ ucfirst($s) }}
            </button>
        </form>
        @endforeach
    </div>
</div>
@else
<span class="lv-status-badge lv-badge-{{ $value ?? 'pending' }}">{{ $label }}</span>
@endif

@if(!empty($approverName) && !empty($value) && $value !== 'pending')
    <small class="text-muted d-block mt-1" style="font-size:11px;">
        <i class="fa fa-user fa-xs"></i> {{ $approverName }}
    </small>
@endif
