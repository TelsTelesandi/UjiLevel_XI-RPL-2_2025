<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiEvent extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'verifikasi_id';
    protected $table = 'verifikasi_event';

    /** @use HasFactory<\Database\Factories\VerifikasiEventFactory> */
    use HasFactory;

    protected $guarded = ['verifikasi_id'];

    public function eventPengajuan()
    {
        return $this->belongsTo(EventPengajuan::class, 'event_id', 'event_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
}
