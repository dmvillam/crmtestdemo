<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // php artisan migrate:refresh --seed
    public function run()
    {
        DB::table('users')->truncate();

        User::create([
            'nombre' => 'Admin',
            'cedula' => rand(0, 4294967295),
            'email1' => 'admin@mypage.com',
            'email2' => 'my_page@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 1,
            'password' => bcrypt('admin'),
            'rol_id' => 1,
        ]);

        User::factory()->count(9)->create();
    }
}
