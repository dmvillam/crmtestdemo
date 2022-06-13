<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cedula_juridica', 'nombre', 'telefono', 'email', 'logo', 'direccion'
    ];

    /*
     * Relations
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}