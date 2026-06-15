<div class="text-center py-5">
    <div class="mb-4">
        <i class="fas {{ $icon }} text-muted opacity-50" style="font-size: 3rem;"></i>
    </div>
    <h5 class="text-muted mb-2">No {{ ucfirst($type) }} Available</h5>
    <p class="text-muted small mb-0">
        @if($type === 'resources')
            No training resources have been assigned yet.
        @else
            No {{ $type }} resources are currently available.
        @endif
    </p>
</div>