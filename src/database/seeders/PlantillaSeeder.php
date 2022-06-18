<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Plantilla;

class PlantillaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plantillas')->truncate();

        Plantilla::create([
            'nombre' => 'Plantilla demo',
            'descripcion_larga' => '<h1>Test</h1><p>Hello world!</p><p>This is an email</p>',
            'descripcion_corta' => 'Hello world, this is a SMS',
        ]);
    }
}
