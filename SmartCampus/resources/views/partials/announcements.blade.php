@if(isset($announcements) && $announcements->count() > 0)
<div class="mb-4">
    @foreach($announcements as $announcement)
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 mb-2" role="alert" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%); border-left: 4px solid var(--sc-primary) !important;">
        <div class="flex-shrink-0">
            <i class="bi bi-megaphone-fill text-primary" style="font-size: 1.25rem;"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $announcement->title }}</h6>
            <p class="mb-1 text-dark" style="font-size: 0.85rem; opacity: 0.85;">{{ $announcement->content }}</p>
            <small class="text-muted" style="font-size: 0.75rem;">
                <i class="bi bi-clock me-1"></i>{{ $announcement->created_at->diffForHumans() }}
            </small>
        </div>
    </div>
    @endforeach
</div>
@endif
