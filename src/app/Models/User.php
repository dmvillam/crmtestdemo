<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'nombre', 'cedula', 'email1', 'email2', 'direccion', 'empresa_id', 'rol_id', 'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function findByEmail($email1)
    {
        return static::where(compact('email1'))->first();
    }

    /*
     * Relations
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }
}
