<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TareaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'tipo_mantenimiento' => ['P1', 'P2', 'P3', 'P4', 'P5'][rand(0,4)],
            'nombre' => 'Tarea '.['P1', 'P2', 'P3', 'P4', 'P5'][rand(0,4)],
            'periodicidad' => rand(1,20)*5,
            'notificacion_id' => 1,
            'user_id' => 1,
        ];
    }
}
