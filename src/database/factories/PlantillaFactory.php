<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlantillaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => 'Plantilla #'.rand(1,20),
            'descripcion_larga' => $this->faker->randomHtml(2,3),
            'descripcion_corta' => $this->faker->sentence(),
        ];
    }
}
