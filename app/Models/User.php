<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Primary key
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
        'nama_lengkap',
        'ekskul',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Relation to events
     */
    public function events()
    {
        return $this->hasMany(EventPengajuan::class, 'user_id', 'user_id');
    }

    /**
     * Relation to verifications (as admin)
     */
    public function verifications()
    {
        return $this->hasMany(VerifikasiEvent::class, 'admin_id', 'user_id');
    }
}
