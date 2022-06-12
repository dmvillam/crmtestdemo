<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('empresas')->truncate();

        /*DB::table('empresas')->insert([
            'cedula_juridica' => 'a',
            'nombre' => 'Lolnada punto o erre ge',
            'telefono' => '+000-0000000',
            'email' => 'contacto@lolnada.com',
            'logo' => 'lolnada.jotapege',
            'direccion' => 'Calle Falsa 123',
        ]);

        \App\Models\Empresa::create([
            'cedula_juridica' => 'a',
            'nombre' => 'Lolnada punto o erre ge',
            'telefono' => '+000-0000000',
            'email' => 'contacto@lolnada.com',
            'logo' => 'lolnada.jotapege',
            'direccion' => 'Calle Falsa 123',
        ]);*/

        \App\Models\Empresa::factory()->count(10)->create();
    }
}
