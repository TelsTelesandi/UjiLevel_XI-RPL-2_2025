<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerifikasiEvent extends Model
{
    use HasFactory;

    protected $table = 'verifikasi_event';
    protected $primaryKey = 'verifikasi_id';

    protected $fillable = [
        'event_id',
        'admin_id',
        'tanggal_verifikasi',
        'catatan_admin',
        'status'
    ];

    public function event()
    {
        return $this->belongsTo(EventPengajuan::class, 'event_id', 'event_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
} 