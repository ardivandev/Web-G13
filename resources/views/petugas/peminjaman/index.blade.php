@extends('layouts.petugas.app')

@section('content')
<audio id="notifAudio" src="{{ asset('sounds/notifikasi.mp3') }}" preload="auto"></audio>

<!-- Meta CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Pusher JS SDK -->
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<!-- Laravel Echo -->
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>

<style>
.btn-primary {
    background-color: #565477;
    border-color: #565477;
}
.btn-primary:hover {
    background-color: #474163;
    border-color: #474163;
}
.btn-primary:active {
    background-color: #474163;
    border-color: #474163;
}

.status-badge {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    color: white !important;
}

.text-danger {
    font-weight: bold;
}

.btn-group form {
    display: inline-block !important;
}

.btn-group .btn {
    margin-right: 5px;
}

.action-buttons {
    white-space: nowrap;
    min-width: 250px;
}

.action-buttons .btn {
    margin-right: 5px;
    margin-bottom: 5px;
}

.barang-list {
    font-size: 0.9rem;
    line-height: 1.3;
    max-width: 200px;
    word-wrap: break-word;
}

.barang-item {
    display: inline-block;
    margin-right: 8px;
    margin-bottom: 4px;
    padding: 2px 6px;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.85rem;
}

.barang-quantity {
    font-weight: bold;
    color: #007bff;
}

.table-success {
    background-color: #d4edda !important;
    transition: background-color 3s ease-out;
}

.table-warning {
    background-color: #fff3cd !important;
    transition: background-color 3s ease-out;
}

#realtime-toast {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.connection-status.connected {
    color: #28a745 !important;
}

.connection-status.connecting {
    color: #ffc107 !important;
}

.connection-status.error {
    color: #dc3545 !important;
}
</style>

{{-- Alerts --}}
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show mt-2" id="success-alert">
    <i class="fas fa-check-circle"></i>
    <strong>Berhasil!</strong> {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show mt-2" id="error-alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Peringatan!</strong>
    <div style="white-space: pre-line; margin-top: 8px;">{{ session('error') }}</div>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show mt-2" id="validation-alert">
    <i class="fas fa-exclamation-circle"></i>
    <strong>Terjadi Kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<h1 class="h3 mb-4 text-gray-800">
    Data Peminjaman
</h1>

<div class="card shadow mb-4 bg-white">
    <div class="card-body">
        <a href="{{ route('petugas.peminjaman.create') }}" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i> Tambah Peminjaman
        </a>

        <div class="card shadow mb-4 bg-white px-3">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="form-inline">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm mr-2" placeholder="Cari nama peminjam">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </form>

                {{-- <!-- Status koneksi real-time -->
                <div id="connection-status" class="small text-muted connection-status connecting">
                    <i class="fas fa-circle"></i> <span id="status-text">Menghubungkan...</span>
                </div> --}}
            </div>

            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-sm" id="peminjaman-table">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Role</th>
                            <th>Nama Peminjam</th>
                            <th>Mapel</th>
                            <th>Ruangan</th>
                            <th>Barang</th>
                            <th>Mulai KBM</th>
                            <th>Selesai KBM</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @php
                            $sortedPeminjaman = $peminjaman->sortByDesc('id_pinjam');
                            $counter = 1;
                        @endphp
                        @forelse ($sortedPeminjaman as $p)
                        <tr id="peminjaman-{{ $p->id_pinjam }}" data-new-row="false">
                            <td>{{ $counter++ }}</td>
                            <td>
                                <span class="badge text-white
                                    @if(strtolower($p->role) === 'siswa') bg-info
                                    @elseif(strtolower($p->role) === 'guru') bg-success
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($p->role ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                @if (strtolower($p->role) === 'siswa')
                                    {{ $p->siswa->nama_siswa ?? 'Tidak ada nama' }}
                                    @if(isset($p->siswa->kelas))
                                        <small class="text-muted d-block">({{ $p->siswa->kelas }})</small>
                                    @endif
                                @elseif (strtolower($p->role) === 'guru')
                                    {{ $p->guru->nama_guru ?? 'Tidak ada nama' }}
                                    <small class="text-muted d-block">Guru</small>
                                @else
                                    <span class="text-muted">Data tidak tersedia</span>
                                @endif
                            </td>
                            <td>{{ $p->mapel->nama_mapel ?? '-' }}</td>
                            <td>{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
                            <td class="barang-list">
                                @forelse($p->detail as $detail)
                                    <span class="barang-item">
                                        {{ $detail->barang->nama_barang ?? 'Unknown' }}(<span class="barang-quantity">{{ $detail->jumlah ?? 0 }}</span>)
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td>{{ $p->mulai_kbm ? \Carbon\Carbon::parse($p->mulai_kbm)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $p->selesai_kbm ? \Carbon\Carbon::parse($p->selesai_kbm)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @php $status = strtolower($p->status ?? 'menunggu'); @endphp
                                <span class="badge status-badge
                                    @if($status=='menunggu') bg-warning
                                    @elseif($status=='dipinjam') bg-success
                                    @elseif($status=='ditolak') bg-danger
                                    @elseif($status=='selesai') bg-info
                                    @else bg-secondary
                                    @endif">
                                    <i class="fas
                                        @if($status=='menunggu') fa-clock
                                        @elseif($status=='dipinjam') fa-check
                                        @elseif($status=='ditolak') fa-times
                                        @elseif($status=='selesai') fa-check-circle
                                        @else fa-question
                                        @endif"></i>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="action-buttons">
                                @include('petugas.peminjaman.actions', ['peminjaman' => $p])
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-row">
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Tidak ada data peminjaman
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($peminjaman, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $peminjaman->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Container for Notifications -->
{{-- <div class="toast-container"></div> --}}
<script>
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Realtime script initialized');

    const audio = document.getElementById('notifAudio');
    const tableBody = document.querySelector('#table-body');

    // Utility: safe text
    function safe(v, fallback = '-') {
        return (v === null || v === undefined || v === '') ? fallback : v;
    }

    function playNotificationSound() {
        console.log('Attempting to play notification sound...');
        if (audio) {
            // Pastikan audio sudah ready
            if (audio.readyState >= 2) {
                audio.currentTime = 0; // Reset ke awal
                audio.play()
                    .then(() => {
                        console.log('Audio played successfully');
                    })
                    .catch(e => {
                        console.error('Audio play failed:', e);
                        // Fallback: coba play tanpa promise
                        try {
                            audio.play();
                        } catch (err) {
                            console.error('Fallback audio play failed:', err);
                        }
                    });
            } else {
                console.warn('Audio not ready, waiting...');
                audio.addEventListener('canplay', function() {
                    audio.currentTime = 0;
                    audio.play().catch(e => console.error('Delayed audio play failed:', e));
                }, { once: true });
            }
        } else {
            console.error('Audio element not found');
        }
    }

    // Test function untuk memastikan audio berfungsi
    function testAudio() {
        console.log('Testing audio...');
        playNotificationSound();
    }

    // Expose test function ke global scope untuk debugging
    window.testAudio = testAudio;

    // Preload audio dan test
    if (audio) {
        audio.addEventListener('loadeddata', function() {
            console.log('Audio loaded successfully');
        });

        audio.addEventListener('error', function(e) {
            console.error('Audio loading error:', e);
        });

        // Set volume
        audio.volume = 0.5;

        console.log('Audio element found:', audio.src);
    }

    // Render satu row peminjaman (returns HTML string)
    function renderRow(p, isNew = false) {
        const status = (p.status || 'menunggu').toLowerCase();

        const roleLower = (p.role || '').toLowerCase();
        const roleBadge = roleLower === 'siswa'
            ? '<span class="badge bg-info text-white">Siswa</span>'
            : roleLower === 'guru'
            ? '<span class="badge bg-success text-white">Guru</span>'
            : '<span class="badge bg-secondary text-white">N/A</span>';

        // Nama user
        let namaUser = '-';
        if (roleLower === 'siswa' && p.siswa) {
            namaUser = safe(p.siswa.nama_siswa, 'Tidak ada nama');
            if (p.siswa.kelas) namaUser += ` <small class="text-muted d-block">(${p.siswa.kelas})</small>`;
        } else if (roleLower === 'guru' && p.guru) {
            namaUser = safe(p.guru.nama_guru, 'Tidak ada nama') + ' <small class="text-muted d-block">Guru</small>';
        }

        const mapel = p.mapel?.nama_mapel || '-';
        const ruangan = p.ruangan?.nama_ruangan || '-';

        const barangHTML = (Array.isArray(p.detail) && p.detail.length > 0)
            ? p.detail.map(d => `<span class="barang-item">${d.barang?.nama_barang || '-'}(<span class="barang-quantity">${d.jumlah ?? 0}</span>)</span>`).join(' ')
            : '<span class="text-muted">-</span>';

        const mulaiKBM = p.mulai_kbm || '-';
        const selesaiKBM = p.selesai_kbm || '-';

        let statusBadgeClass = 'bg-secondary', statusIcon = 'fa-question';
        if (status === 'menunggu') { statusBadgeClass = 'bg-warning'; statusIcon = 'fa-clock'; }
        else if (status === 'dipinjam') { statusBadgeClass = 'bg-success'; statusIcon = 'fa-check'; }
        else if (status === 'ditolak') { statusBadgeClass = 'bg-danger'; statusIcon = 'fa-times'; }
        else if (status === 'selesai') { statusBadgeClass = 'bg-info'; statusIcon = 'fa-check-circle'; }

        // CSRF token for forms
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const editUrl = `/petugas/peminjaman/${p.id_pinjam}/edit`;
        const deleteUrl = `/petugas/peminjaman/${p.id_pinjam}`;
        const updateStatusUrl = `/petugas/peminjaman/${p.id_pinjam}/status`;

        const actionButtons = `
            <a href="${editUrl}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <form action="${deleteUrl}" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus?')">
                <input type="hidden" name="_token" value="${csrf}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
            ${status === 'menunggu' ? `
                <form action="${updateStatusUrl}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="status" value="Dipinjam">
                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Yakin ingin menyetujui peminjaman ini?')">
                        <i class="fas fa-check"></i> Disetujui
                    </button>
                </form>
                <form action="${updateStatusUrl}" method="POST" style="display:inline-block;">
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="status" value="Ditolak">
                    <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Yakin ingin menolak peminjaman ini?')">
                        <i class="fas fa-times"></i> Ditolak
                    </button>
                </form>
            ` : '' }
        `;

        const rowClass = isNew ? 'table-success' : '';
        const counter = isNew ? 'Baru' : (document.querySelectorAll('#table-body tr').length + 1);

        return `
            <tr id="peminjaman-${p.id_pinjam}" class="${rowClass}" data-new-row="${isNew}">
                <td>${counter}</td>
                <td>${roleBadge}</td>
                <td>${namaUser}</td>
                <td>${mapel}</td>
                <td>${ruangan}</td>
                <td class="barang-list">${barangHTML}</td>
                <td>${mulaiKBM}</td>
                <td>${selesaiKBM}</td>
                <td>
                    <span class="badge status-badge ${statusBadgeClass}">
                        <i class="fas ${statusIcon}"></i> ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </td>
                <td class="action-buttons">${actionButtons}</td>
            </tr>
        `;
    }

    function updateExistingRow(p) {
        const existingRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
        if (existingRow) {
            existingRow.outerHTML = renderRow(p, false);
            const newRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
            if (newRow) {
                newRow.classList.add('table-warning');
                setTimeout(() => newRow.classList.remove('table-warning'), 3000);
            }
        }
    }

    function insertNewRowTop(p) {
        removeEmptyRow();
        tableBody.insertAdjacentHTML('afterbegin', renderRow(p, true));
        const newRow = document.getElementById(`peminjaman-${p.id_pinjam}`);
        if (newRow) {
            setTimeout(() => newRow.classList.remove('table-success'), 5000);
        }
    }

    function removeEmptyRow() {
        const emptyRow = document.getElementById('empty-row');
        if (emptyRow) emptyRow.remove();
    }

    // Enable user interaction untuk audio (required untuk autoplay)
    let userInteracted = false;

    document.addEventListener('click', function enableAudio() {
        if (!userInteracted) {
            userInteracted = true;
            console.log('User interaction detected - audio enabled');

            // Pre-load audio dengan user interaction
            if (audio && audio.paused) {
                audio.play().then(() => {
                    audio.pause();
                    audio.currentTime = 0;
                    console.log('Audio primed for future playback');
                }).catch(e => {
                    console.log('Audio priming failed:', e);
                });
            }
        }
    }, { once: true });

    try {
        // Enable pusher debug
        if (window.Pusher && window.Pusher.logToConsole) {
            window.Pusher.logToConsole = true;
        }

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env("PUSHER_APP_KEY") }}',
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            forceTLS: true
        });

        // Connection events
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            const conn = window.Echo.connector.pusher.connection;
            conn.bind('connected', function() {
                console.log('Pusher connected');
            });
            conn.bind('disconnected', function() {
                console.warn('Pusher disconnected');
            });
            conn.bind('error', function(err) {
                console.error('Pusher error', err);
            });
        }

        const channel = window.Echo.channel('gudang13');

        let firstLoad = true;

        // Event: peminjaman baru - PERBAIKAN UTAMA
        channel.listen('.peminjaman.baru', (e) => {
            console.log('Event .peminjaman.baru received:', e);
            if (e && e.peminjaman) {
                console.log('Processing new peminjaman...');
                insertNewRowTop(e.peminjaman);

                 const existingRow = document.getElementById(`peminjaman-${e.peminjaman.id_pinjam}`);

        if (existingRow) {
            // Kalau sudah ada → update aja, JANGAN bunyi
            updateExistingRow(e.peminjaman);
        } else {
            // Kalau bener2 baru → insert + bunyi
            insertNewRowTop(e.peminjaman);
            playNotificationSound();
        }
            } else {
                console.warn('Invalid peminjaman data received:', e);
            }
        });

        // Event: status update - dengan audio untuk status tertentu
        channel.listen('.peminjaman.status.update', (e) => {
            console.log('Event .peminjaman.status.update received:', e);
            if (e && e.peminjaman) {
                updateExistingRow(e.peminjaman);
            }
        });

        // Setelah halaman selesai load, reset flag jadi false
          window.addEventListener('load', () => {
              setTimeout(() => {
                  isFirstLoad = false;
              }, 1500); // kasih delay biar aman
          });

        console.log('Echo listeners attached for channel gudang13');

    } catch (err) {
        console.error('Realtime init error:', err);
    }

    // Debug helper - tambahkan tombol test di console
    console.log('Use testAudio() in console to test audio playback');
});
</script>

@endsection
