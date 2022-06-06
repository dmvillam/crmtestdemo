<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();

        \App\Models\Rol::create([
            'nombre' => 'Administrador',
        ]);

        \App\Models\Rol::create([
            'nombre' => 'Mantenimiento',
        ]);

        \App\Models\Rol::create([
            'nombre' => 'Cliente',
        ]);
    }
}
