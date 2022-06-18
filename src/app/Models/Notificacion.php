<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre', 'user_id', 'plantilla_id', 'telefono', 'email', 'notificar_email', 'notificar_sms'
    ];

    /*
     * Relations
     */
    public function tarea()
    {
        return $this->hasOne(Tarea::class);
    }
}
