<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'photo_path',
        'photo_name'
    ];

    public function event()
    {
        return $this->belongsTo(EventPengajuan::class, 'event_id', 'event_id');
    }
} 