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
    function it_shows_the_tasks_list_page()
    {
        $fake_user = $this->getFakeUser();
        Empresa::factory()->create(['nombre' => 'Company 1']);
        Empresa::factory()->create(['nombre' => 'Company 2']);

        $response = $this->actingAs($fake_user)
            ->get(route('companies.index'))
            ->assertStatus(200)
            ->assertViewIs(route('users.index'))
            ->assertViewHas('empresas')
            ->assertSee('Empresas')
            ->assertSee('Company 1')
            ->assertSee('Company 2');
    }
}
