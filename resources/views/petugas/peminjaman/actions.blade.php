<a href="{{ route('petugas.peminjaman.edit', $peminjaman->id_pinjam) }}" class="btn btn-sm btn-warning">
    <i class="fas fa-edit"></i> Edit
</a>

<form action="{{ route('petugas.peminjaman.destroy', $peminjaman->id_pinjam) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus peminjaman ini?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">
        <i class="fas fa-trash"></i> Hapus
    </button>
</form>

{{-- Tombol Disetujui / Ditolak hanya tampil jika status masih "Menunggu" --}}
@if($peminjaman->status === 'menunggu')
    <form action="{{ route('petugas.peminjaman.updateStatus', $peminjaman->id_pinjam) }}"
          method="POST" class="d-inline"
          onsubmit="return confirm('Yakin ingin menyetujui peminjaman ini?')">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="dipinjam">
        <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-check"></i> Disetujui
        </button>
    </form>

    <form action="{{ route('petugas.peminjaman.updateStatus', $peminjaman->id_pinjam) }}"
          method="POST" class="d-inline"
          onsubmit="return confirm('Yakin ingin menolak peminjaman ini?')">
        @csrf
        @method('PATCH')
        <input type="hidden" name="status" value="ditolak">
        <button type="submit" class="btn btn-sm btn-secondary">
            <i class="fas fa-times"></i> Ditolak
        </button>
    </form>
@endif
