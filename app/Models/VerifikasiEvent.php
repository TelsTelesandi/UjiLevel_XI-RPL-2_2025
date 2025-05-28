<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiEvent extends Model
{
    use HasFactory;

    protected $primaryKey = 'verifikasi_id';
    
    protected $fillable = [
        'event_id', 'admin_id', 'tanggal_verifikasi', 'catatan_admin', 'status'
    ];

    public function event()
    {
        return $this->belongsTo(EventPengajuan::class, 'event_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}