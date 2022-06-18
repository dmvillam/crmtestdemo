<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rol;
use App\Models\Tarea;
use App\Models\Notificacion;

class TasksModuleTest extends TestCase
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
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1']);
        $notificacion2 = Notificacion::factory()->create(['nombre' => 'Notificación 2']);
        $tarea1 = Tarea::factory()->create(['nombre' => 'Tarea 1']);
        $tarea2 = Tarea::factory()->create(['nombre' => 'Tarea 2']);
        $tarea1->notificacion_id = $notificacion1->id;
        $tarea2->notificacion_id = $notificacion2->id;
        Rol::create(['nombre' => 'Cliente']);
        $cliente = User::factory()->create();
        $tarea1->user_id = $cliente->id;
        $tarea2->user_id = $cliente->id;
        $tarea1->save();
        $tarea2->save();

        $response = $this->actingAs($fake_user)
            ->get(route('tasks.index'))
            ->assertStatus(200)
            ->assertViewIs('tasks.index')
            ->assertViewHas('tareas')
            ->assertViewHas('tipos_mantenimiento')
            ->assertViewHas('plantillas')
            ->assertViewHas('clientes')
            ->assertSee('Tareas')
            ->assertSee('Tarea 1')
            ->assertSee('Tarea 2')
            ->assertSee($cliente->nombre);
    }
}
