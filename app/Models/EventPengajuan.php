<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventPengajuan extends Model
{
    use HasFactory;

    protected $table = 'event_pengajuan';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'user_id',
        'judul_event',
        'jenis_kegiatan',
        'total_pembiayaan',
        'proposal',
        'deskripsi',
        'tanggal_pengajuan',
        'status'
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiEvent::class, 'event_id', 'event_id');
    }

    public function photos()
    {
        return $this->hasMany(EventPhoto::class, 'event_id', 'event_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDiverifikasi($query)
    {
        return $query->where('status', 'diverifikasi');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isDiverifikasi()
    {
        return $this->status === 'diverifikasi';
    }

    public function isDitolak()
    {
        return $this->status === 'ditolak';
    }
} 