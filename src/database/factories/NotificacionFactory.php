<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => 'Notificación de tarea #'.rand(1,20),
            'user_id' => 0,
            'plantilla_id' => 1,
            'telefono' => $this->faker->tollFreePhoneNumber(),
            'email' => $this->faker->companyEmail(),
            'notificar_email' => rand(0,1),
            'notificar_sms' => rand(0,1),
        ];
    }
}
