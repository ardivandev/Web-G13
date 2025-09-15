@extends('layouts.pengguna.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Riwayat Peminjaman</h2>

    @if($peminjaman->isEmpty())
        <div class="alert alert-info">Belum ada peminjaman yang tercatat.</div>
    @else
        <div class="row">
            @foreach($peminjaman as $p)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                {{ $p->siswa?->nama ?? 'Siswa Tidak Diketahui' }}
                            </h5>
                            <p class="card-text mb-1"><strong>Guru:</strong> {{ $p->guru?->nama ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Mapel:</strong> {{ $p->mapel?->nama_mapel ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Ruangan:</strong> {{ $p->ruangan?->nama ?? '-' }}</p>

                            <p class="card-text mb-1"><strong>Barang:</strong>
                                @forelse($p->detail as $d)
                                    <span class="badge bg-secondary">{{ $d->barang?->nama_barang }}</span>
                                @empty
                                    <span class="text-muted">Tidak ada</span>
                                @endforelse
                            </p>

                            <p class="card-text mb-1"><strong>Mulai:</strong> {{ $p->mulai_kbm ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Selesai:</strong> {{ $p->selesai_kbm ?? '-' }}</p>

                            <p class="card-text mb-2">
                                <strong>Status:</strong>
                                @if($p->status === 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($p->status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($p->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($p->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="card-footer text-muted small">
                            Dibuat: {{ $p->created_at ? $p->created_at->format('d M Y H:i') : '-' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary mt-3">← Kembali</a>
</div>
@endsection
