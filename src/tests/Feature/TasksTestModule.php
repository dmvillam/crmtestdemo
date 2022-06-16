<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Tarea;

class TasksTestModule extends TestCase
{
    use RefreshDatabase;

    private function getFakeUser()
    {
        return new User([
            'id' => 1,
            'nombre' => 'a',
            'cedula' => 123456,
            'email1' => 't1@t.t',
            'email2' => 't2@t.t',
            'direccion' => 'a',
            'empresa_id' => 1,
            'rol_id' => 1,
        ]);
    }
    
    /** @test **/
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
