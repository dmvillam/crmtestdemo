<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'cedula_juridica' => Str::random(12),
            'nombre' => $this->faker->unique()->company(),
            'telefono' => $this->faker->unique()->tollFreePhoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'logo' => $this->faker->unique()->imageUrl(200, 200),
            'direccion' => $this->faker->unique()->address(),
        ];
    }
}
