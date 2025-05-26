<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPengajuan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_pengajuan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'judul_event',
        'jenis_kegiatan',
        'total_pembiayaan',
        'proposal',
        'deskripsi',
        'tanggal_pengajuan',
        'status',
    ];

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the verification associated with the event.
     */
    public function verifikasi()
    {
        return $this->hasOne(VerifikasiEvent::class, 'event_id', 'event_id');
    }
} 