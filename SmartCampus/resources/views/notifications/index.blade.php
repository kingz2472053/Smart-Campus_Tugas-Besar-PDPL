@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center justify-content-between">
        <div class="col-auto">
            <h1 class="h3 text-gray-800 mb-0"><i class="bi bi-bell-fill text-primary"></i> Pusat Notifikasi</h1>
            <p class="text-muted mb-0">Kelola semua pemberitahuan dan informasi penting akademis Anda di sini.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            @if($notifications->where('is_read', false)->count() > 0 || $status === 'unread' || $status === 'all')
                <button id="btnMarkAllRead" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm transition">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Terbaca
                </button>
            @endif
        </div>
    </div>

    <!-- Filter Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <!-- Status Filter Pills -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-medium me-2"><i class="bi bi-funnel"></i> Filter:</span>
                        <a href="{{ route('notifications.index', ['status' => 'all']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-medium transition {{ $status === 'all' ? 'btn-primary' : 'btn-light text-secondary' }}">
                            Semua
                        </a>
                        <a href="{{ route('notifications.index', ['status' => 'unread']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-medium transition {{ $status === 'unread' ? 'btn-primary' : 'btn-light text-secondary' }}">
                            Belum Dibaca
                        </a>
                        <a href="{{ route('notifications.index', ['status' => 'read']) }}" 
                           class="btn btn-sm rounded-pill px-3 fw-medium transition {{ $status === 'read' ? 'btn-primary' : 'btn-light text-secondary' }}">
                            Sudah Dibaca
                        </a>
                    </div>

                    <!-- Channel Legend -->
                    <div class="d-flex align-items-center gap-3 text-muted small">
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-circle p-1"><i class="bi bi-layout-text-window-reverse"></i></span> Web
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span class="badge bg-info bg-opacity-10 text-info rounded-circle p-1"><i class="bi bi-envelope"></i></span> E-mail
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification List Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        @if($notifications->isEmpty())
            <div class="card-body py-5 text-center">
                <div class="mb-3">
                    <span class="display-1 text-muted"><i class="bi bi-bell-slash"></i></span>
                </div>
                <h5 class="fw-semibold text-dark">Tidak ada notifikasi</h5>
                <p class="text-muted mb-0">Semua pemberitahuan Anda akan muncul di halaman ini.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm mt-3 px-4 rounded-pill shadow-sm">
                    Kembali ke Dashboard
                </a>
            </div>
        @else
            <div class="list-group list-group-flush" id="notificationList">
                @foreach($notifications as $notif)
                    <div class="list-group-item list-group-item-action p-4 border-0 border-bottom transition position-relative {{ !$notif->is_read ? 'bg-light border-start border-4 border-primary' : '' }}" 
                         id="notif-row-{{ $notif->id }}">
                        
                        <div class="d-flex align-items-start gap-3">
                            <!-- Icon Channel -->
                            <div class="mt-1">
                                @if($notif->channel === 'email')
                                    <span class="avatar-icon bg-info bg-opacity-10 text-info rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-envelope-fill fs-5"></i>
                                    </span>
                                @else
                                    <span class="avatar-icon bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                        <i class="bi bi-layout-text-window-reverse fs-5"></i>
                                    </span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-grow-1">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-1">
                                    
                                    <h6 class="mb-0 fw-semibold text-dark mt-1">
                                        @if($notif->assignment)
                                            {{ $notif->assignment->course?->name ?? 'Mata Kuliah' }} — {{ $notif->assignment->title }}
                                        @else
                                            Pemberitahuan Sistem
                                        @endif
                                    </h6>
                                    
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="text-muted small fw-medium text-nowrap">
                                            <i class="bi bi-clock me-1"></i>{{ $notif->sent_at ? $notif->sent_at->diffForHumans() : $notif->created_at->diffForHumans() }}
                                        </span>
                                        
                                        <div class="d-flex align-items-center gap-2 border-start ps-3">
                                            @if(!$notif->is_read)
                                                <button class="btn btn-link text-primary p-0 btn-mark-read" data-id="{{ $notif->id }}" title="Tandai Terbaca">
                                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0" title="Hapus Notifikasi">
                                                    <i class="bi bi-trash3-fill fs-5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <p class="text-secondary small mb-0">{{ $notif->message }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .transition {
        transition: all 0.25s ease-in-out;
    }
    .list-group-item:hover {
        background-color: #f8fafc !important;
    }
    .btn-link {
        text-decoration: none;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .btn-link:hover {
        opacity: 1;
    }
    .avatar-icon i {
        line-height: 1;
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Klik tombol tandai satu terbaca
    const markReadButtons = document.querySelectorAll('.btn-mark-read');
    markReadButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const row = document.getElementById(`notif-row-${id}`);
            const btn = this;

            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.classList.remove('bg-light', 'border-start', 'border-4', 'border-primary');
                    btn.remove(); // Hapus tombol centang
                    
                    // Trigger pembaruan dropdown lonceng jika ada di halaman
                    if (typeof refreshLonceng === 'function') {
                        refreshLonceng();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Klik tombol tandai semua terbaca
    const btnMarkAllRead = document.getElementById('btnMarkAllRead');
    if (btnMarkAllRead) {
        btnMarkAllRead.addEventListener('click', function (e) {
            e.preventDefault();
            
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hilangkan border & background aktif pada semua item list
                    const rows = document.querySelectorAll('[id^="notif-row-"]');
                    rows.forEach(row => {
                        row.classList.remove('bg-light', 'border-start', 'border-4', 'border-primary');
                    });
                    
                    // Hilangkan semua tombol centang
                    document.querySelectorAll('.btn-mark-read').forEach(btn => btn.remove());
                    
                    // Hilangkan tombol tandai semua terbaca
                    btnMarkAllRead.remove();

                    // Refresh lonceng
                    if (typeof refreshLonceng === 'function') {
                        refreshLonceng();
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>
@endpush
