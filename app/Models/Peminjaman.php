<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'tbl_peminjaman';
    protected $primaryKey = 'id_pinjam';
    public $timestamps = false;

    protected $fillable = [
        'role',
        'id_siswa',
        'id_guru',
        'id_mapel',
        'id_petugas',
        'id_ruangan',
        'no_telp',
        'mulai_kbm',
        'selesai_kbm',
        'jaminan',
        'status',
        'tanggal_pinjam'
    ];

    // Relasi ke siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Relasi ke guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    // Relasi ke mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    // Relasi ke ruangan
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    // Relasi ke petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    // Relasi ke detail peminjaman
    public function detail()
    {
        return $this->hasMany(DetailPeminjaman::class, 'id_pinjam', 'id_pinjam');
    }

    // Relasi ke pengembalian
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_pinjam', 'id_pinjam');
    }

    // Accessor untuk mendapatkan nama peminjam
    public function getNamaPeminjamAttribute()
    {
        if ($this->role === 'siswa' && $this->siswa) {
            return $this->siswa->nama_siswa;
        } elseif ($this->role === 'guru' && $this->guru) {
            return $this->guru->nama_guru;
        }

        return 'Data tidak tersedia';
    }

    // Accessor untuk mendapatkan kelas peminjam (jika siswa)
    public function getKelasPeminjamAttribute()
    {
        if ($this->role === 'siswa' && $this->siswa) {
            return $this->siswa->kelas ?? '-';
        }

        return null;
    }

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan role
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Method untuk cek apakah peminjaman masih aktif
    public function isActive()
    {
        return in_array($this->status, ['Menunggu', 'Dipinjam']);
    }

    // Method untuk cek apakah peminjaman sudah selesai
    public function isCompleted()
    {
        return $this->status === 'Selesai';
    }

    // Method untuk cek apakah peminjaman ditolak
    public function isRejected()
    {
        return $this->status === 'Ditolak';
    }
}
