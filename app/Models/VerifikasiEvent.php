<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verifikasi_event';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'verifikasi_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'admin_id',
        'tanggal_verifikasi',
        'catatan_admin',
        'komentar',
        'komentar_at',
        'status',
    ];

    /**
     * Get the event associated with the verification.
     */
    public function event()
    {
        return $this->belongsTo(EventPengajuan::class, 'event_id', 'event_id');
    }

    /**
     * Get the admin that performed the verification.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }
} 