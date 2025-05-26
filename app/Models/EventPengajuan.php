<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPengajuan extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'event_id';
    protected $table = 'event_pengajuan';

    /** @use HasFactory<\Database\Factories\EventPengajuanFactory> */
    use HasFactory;

    protected $guarded = ['event_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function verifikasiEvent()
    {
        return $this->hasOne(VerifikasiEvent::class, 'event_id', 'event_id');
    }
}
