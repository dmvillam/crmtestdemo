<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_mantenimiento', 'nombre', 'periodicidad', 'notificacion_id', 'user_id'
    ];

    /*
     * Relations
     */
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class);
    }
}
