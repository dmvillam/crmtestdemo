<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rol;
use App\Models\Tarea;
use App\Models\Notificacion;
use App\Models\Plantilla;

class ReportesModuleTest extends TestCase
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
    public function it_shows_the_reports_list()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $rol_admin = Rol::create(['nombre' => 'Admin']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente2 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente3 = User::factory()->create(['rol_id'=>$rol_admin->id]);
        $plantilla1 = Plantilla::factory()->create();
        $plantilla2 = Plantilla::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1', 'plantilla_id' => $plantilla1->id]);
        $notificacion2 = Notificacion::factory()->create(['nombre' => 'Notificación 2', 'plantilla_id' => $plantilla2->id]);
        $tarea1 = Tarea::factory()->create([
            'nombre'=>'Tarea 1', 'notificacion_id'=>$notificacion1->id, 'user_id'=>$cliente1->id
        ]);
        $tarea2 = Tarea::factory()->create([
            'nombre'=>'Tarea 2', 'notificacion_id'=>$notificacion2->id, 'user_id'=>$cliente2->id
        ]);

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index'))
            ->assertStatus(200)
            ->assertViewIs('reports.index')
            ->assertViewHas('notificaciones')
            ->assertViewHas('clientes')
            ->assertSee('Reportes')
            ->assertSee("<td>{$cliente1->nombre}</td>", false)
            ->assertSee("<td>{$cliente2->nombre}</td>", false)
            ->assertDontSee("<td>{$cliente3->nombre}</td>", false);
    }

    /** @test **/
    public function it_shows_a_default_message_if_the_report_list_is_empty()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $rol_admin = Rol::create(['nombre' => 'Admin']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente2 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente3 = User::factory()->create(['rol_id'=>$rol_admin->id]);
        $plantilla1 = Plantilla::factory()->create();
        $plantilla2 = Plantilla::factory()->create();

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index'))
            ->assertStatus(200)
            ->assertSee('No hay notificaciones para mostrar por el momento...');
    }

    /** @test **/
    public function it_shows_the_reports_list_with_single_client()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente2 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $plantilla1 = Plantilla::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre'=>'Notificación 1', 'plantilla_id'=>$plantilla1->id, 'user_id'=>$cliente1->id]);
        $notificacion2_1 = Notificacion::factory()->create(['nombre'=>'Notificación 2-1', 'plantilla_id'=>$plantilla1->id, 'user_id'=>$cliente2->id]);
        $notificacion2_2 = Notificacion::factory()->create(['nombre'=>'Notificación 2-2', 'plantilla_id'=>$plantilla1->id, 'user_id'=>$cliente2->id]);
        $tarea1 = Tarea::factory()->create(['nombre'=>'Tarea 1', 'notificacion_id'=>$notificacion1->id, 'user_id'=>$cliente1->id]);
        $tarea2_1 = Tarea::factory()->create(['nombre'=>'Tarea 2-1', 'notificacion_id'=>$notificacion2_1->id, 'user_id'=>$cliente2->id]);
        $tarea2_2 = Tarea::factory()->create(['nombre'=>'Tarea 2-2', 'notificacion_id'=>$notificacion2_2->id, 'user_id'=>$cliente2->id]);

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index')."/".$cliente2->id)
            ->assertStatus(200)
            ->assertViewIs('reports.index')
            ->assertViewHas('notificaciones')
            ->assertViewHas('clientes')
            ->assertSee('Reportes')
            ->assertSee("data-notificacion-id=\"{$notificacion2_1->id}\"", false)
            ->assertSee("data-notificacion-id=\"{$notificacion2_2->id}\"", false)
            ->assertSee("<td>{$cliente2->nombre}</td>", false)
            ->assertDontSee("<td>{$cliente1->nombre}</td>", false);
    }

    /** @test **/
    public function it_shows_the_reports_list_with_daterange()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $plantilla1 = Plantilla::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1', 'plantilla_id' => $plantilla1->id, 'user_id' => $cliente1->id, 'next_activity' => '2022-06-01 12:00:00']);
        $notificacion2 = Notificacion::factory()->create(['nombre' => 'Notificación 2', 'plantilla_id' => $plantilla1->id, 'user_id' => $cliente1->id, 'next_activity' => '2022-06-02 12:00:00']);
        $notificacion3 = Notificacion::factory()->create(['nombre' => 'Notificación 3', 'plantilla_id' => $plantilla1->id, 'user_id' => $cliente1->id, 'next_activity' => '2022-06-03 12:00:00']);
        $tarea1 = Tarea::factory()->create(['nombre'=>'Tarea 1','notificacion_id'=>$notificacion1->id,'user_id'=>$cliente1->id]);
        $tarea2 = Tarea::factory()->create(['nombre'=>'Tarea 2','notificacion_id'=>$notificacion2->id,'user_id'=>$cliente1->id]);
        $tarea3 = Tarea::factory()->create(['nombre'=>'Tarea 3','notificacion_id'=>$notificacion3->id,'user_id'=>$cliente1->id]);

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index')."/0/0/06-02-2022 - 07-01-2022")
            ->assertStatus(200)
            ->assertViewIs('reports.index')
            ->assertViewHas('notificaciones')
            ->assertViewHas('clientes')
            ->assertSee('Reportes')
            ->assertSee($notificacion2->next_activity)
            ->assertSee($notificacion3->next_activity)
            ->assertDontSee($notificacion1->next_activity);
    }

    /** @test **/
    public function it_shows_a_default_message_if_client_not_found()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $cliente2 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $plantilla1 = Plantilla::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1', 'plantilla_id' => $plantilla1->id, 'user_id' => $cliente1->id]);
        $tarea1 = Tarea::factory()->create(['nombre' => 'Tarea 1', 'notificacion_id' => $notificacion1->id, 'user_id' => $cliente1->id]);

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index')."/".$cliente2->id)
            ->assertStatus(200)
            ->assertSee('No hay notificaciones para mostrar por el momento...');
    }

    /** @test **/
    public function it_shows_a_default_message_if_reports_in_daterange_not_found()
    {
        $fake_user = $this->getFakeUser();

        $rol_cliente = Rol::create(['nombre' => 'Cliente']);
        $cliente1 = User::factory()->create(['rol_id'=>$rol_cliente->id]);
        $plantilla1 = Plantilla::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1', 'plantilla_id' => $plantilla1->id, 'user_id' => $cliente1->id, 'next_activity' => '2022-06-01 12:00:00']);
        $tarea1 = Tarea::factory()->create(['nombre' => 'Tarea 1', 'notificacion_id' => $notificacion1->id, 'user_id' => $cliente1->id]);

        $response = $this->actingAs($fake_user)
            ->get(route('reports.index')."/0/0/06-02-2022 - 06-02-2022")
            ->assertStatus(200)
            ->assertSee('No hay notificaciones para mostrar por el momento...');
    }
}
