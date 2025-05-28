<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPengajuan extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';
    
    protected $fillable = [
        'user_id', 'judul_event', 'jenis_kegiatan', 'total_pembiayaan',
        'proposal', 'deskripsi', 'tanggal_pengajuan', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikasi()
    {
        return $this->hasOne(VerifikasiEvent::class, 'event_id');
    }
}