<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rol;
use App\Models\Tarea;
use App\Models\Notificacion;
use App\Models\Plantilla;

use App\Mail\NotificacionMail;

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
        $cliente1 = User::factory()->create();
        $cliente2 = User::factory()->create();
        $notificacion1 = Notificacion::factory()->create(['nombre' => 'Notificación 1']);
        $notificacion2 = Notificacion::factory()->create(['nombre' => 'Notificación 2']);
        $tarea1 = Tarea::factory()->create([
            'nombre'=>'Tarea 1', 'notificacion_id'=>$notificacion1->id, 'user_id'=>$cliente1->id
        ]);
        $tarea2 = Tarea::factory()->create([
            'nombre'=>'Tarea 2', 'notificacion_id'=>$notificacion2->id, 'user_id'=>$cliente2->id
        ]);
        Rol::create(['nombre' => 'Cliente']);

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
            ->assertSee($cliente1->nombre)
            ->assertSee($cliente2->nombre);
    }

    /** @test **/
    function it_shows_a_default_message_if_the_task_list_is_empty()
    {
        $fake_user = $this->getFakeUser();
        Rol::create(['nombre' => 'Cliente']);
        $response = $this->actingAs($fake_user)->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertSee('No hay tareas para mostrar por el momento...');
    }

    /** @test **/
    function it_displays_the_task_details()
    {
        $fake_user = $this->getFakeUser();

        $usuario = User::factory()->create();
        $tarea = Tarea::factory()->create(['user_id'=>$usuario->id]);

        $response = $this->actingAs($fake_user)
            ->getJson(route('tasks.show', $tarea))
            ->assertStatus(200)
            ->assertJson([
                'task'=>$tarea->nombre,
            ]);

        $response = $this->actingAs($fake_user)
            ->get(route('tasks.show', $tarea))
            ->assertStatus(200)
            ->assertSee($tarea->nombre)
            ->assertSee($tarea->tipo_mantenimiento)
            ->assertSee("Cada {$tarea->periodicidad} min")
            ->assertSee($tarea->cliente->nombre);
    }

    /** @test **/
    function it_displays_the_task2_details()
    {
        $fake_user = $this->getFakeUser();

        $usuario = User::factory()->create();
        Tarea::factory()->count(49)->create();
        $tarea = Tarea::factory()->create(['user_id'=>$usuario->id]);

        $response = $this->actingAs($fake_user)
            ->getJson(route('tasks.show', $tarea))
            ->assertStatus(200)
            ->assertJson(['task'=>$tarea->nombre]);
    }

    /** @test **/
    function it_displays_a_404_error_if_the_task_is_not_found()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)
            ->get(route('tasks.show', '1'))
            ->assertStatus(404);
    }

    /** @test **/
    function it_loads_the_new_task_page()
    {
        $fake_user = $this->getFakeUser();
        Rol::create(['nombre' => 'Cliente']);
        $this->actingAs($fake_user)
            ->get(route('tasks.index'))
            ->assertStatus(200)
            ->assertSee('Crear nueva tarea');
    }

    /** @test **/
    function it_creates_a_new_task_without_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->post(route('tasks.store'), [
            'tipo_mantenimiento'    => 'P1',
            'nombre'                => 'Tarea sin Notificación',
            'periodicidad'          => 5,
            'user_id'               => 1,
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tareas', [
            'tipo_mantenimiento'    => 'P1',
            'nombre'                => 'Tarea sin Notificación',
            'periodicidad'          => 5,
            'user_id'               => 1,
            'notificacion_id'       => null,
        ]);
        $this->assertEquals(0, Notificacion::count());
    }

    /** @test **/
    function it_creates_a_new_task_with_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->post(route('tasks.store'), [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea con Notificación',
            'user_id'               => 1,
            'periodicidad'          => 10,
            'notif_nombre'          => 'Notificación de prueba',
            'plantilla_id'          => 1,
            'telefono'              => '555 555-555-555',
            'email'                 => 'test@mail.com',
            'notificacion'          => 'a',
            'notificar_email'       => '1',
            //'notificar_sms'         => '1',
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tareas', ['nombre' => 'Tarea con Notificación']);
        $this->assertDatabaseHas('notificaciones', [
            'nombre'            => 'Notificación de prueba',
            'plantilla_id'      => 1,
            'telefono'          => '555 555-555-555',
            'email'             => 'test@mail.com',
            'notificar_email'   => '1',
            'notificar_sms'     => '0',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_on_task_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->post(route('tasks.store'), [
                'tipo_mantenimiento'    => '',
                'nombre'                => '',
                'user_id'               => '',
                'periodicidad'          => '',
                'notif_nombre'          => '',
                'plantilla_id'          => '',
                'telefono'              => '',
                'email'                 => '',
                'notificacion'          => 'on',
                'notificar_email'       => '1',
                'notificar_sms'         => '1',
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors([
                'tipo_mantenimiento', 'nombre', 'user_id', 'periodicidad', 'notif_nombre', 'plantilla_id', 'telefono', 'email'
            ], null, 'store');

        $this->assertEquals(0, Tarea::count());
    }

    /** @test **/
    function periodicidad_must_be_numeric_on_task_storing()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)->post(route('tasks.store'), [
            'tipo_mantenimiento'    => 'a',
            'nombre'                => 'a',
            'periodicidad'          => 'a',
            'user_id'               => 1,
        ])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHasErrors(['periodicidad'], null, 'store');
    }

    /** @test **/
    function periodicidad_must_be_multiple_of_5_on_task_storing()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)->post(route('tasks.store'), [
            'tipo_mantenimiento'    => 'a',
            'nombre'                => 'a',
            'periodicidad'          => 12,
            'user_id'               => 1,
        ])->assertRedirect(route('tasks.index'))
        ->assertSessionHasErrors(['periodicidad'], null, 'store');
    }

    /** @test **/
    function the_notif_email_must_be_valid_on_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->post(route('tasks.store'), [
                'tipo_mantenimiento'    => 'P2',
                'nombre'                => 'Tarea con Notificación',
                'user_id'               => 1,
                'periodicidad'          => 10,
                'notif_nombre'          => 'Notificación de prueba',
                'plantilla_id'          => 1,
                'telefono'              => '555 555-555-555',
                'email'                 => 'non_valid_email',
                'notificacion'          => 'a',
                //'notificar_email'       => '1',
                'notificar_sms'         => '1',
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors(['email'], null, 'store');

        $this->assertEquals(0, Tarea::count());
    }

    /** @test **/
    function it_retrieves_correctly_the_task_editing_info()
    {
        $fake_user = $this->getFakeUser();
        $notificacion = Notificacion::factory()->create(['nombre'=>'Notificación de prueba']);
        $tarea = Tarea::factory()->create(['nombre'=>'Tarea de prueba', 'notificacion_id'=>$notificacion->id]);
        $this->actingAs($fake_user)
            ->getJson(route('tasks.edit', $tarea))
            ->assertStatus(200)
            ->assertJson(['nombre'=>'Tarea de prueba'])
            ->assertJson(['notif_nombre'=>'Notificación de prueba']);
    }

    /** @test **/
    function it_updates_simple_task_without_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $this->assertEquals(0, Notificacion::count());
        $tarea = Tarea::factory()->create(['notificacion_id'=>null]);
        $this->actingAs($fake_user)->put(route('tasks.update', $tarea), [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea con Notificación',
            'user_id'               => 1,
            'periodicidad'          => 10,
        ])->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tareas', [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea con Notificación',
            'user_id'               => 1,
            'periodicidad'          => 10,
            'notificacion_id'       => null,
        ]);
        $this->assertEquals(0, Notificacion::count());
    }

    /** @test **/
    function it_updates_a_simple_task_with_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $this->assertEquals(0, Notificacion::count());
        $tarea = Tarea::factory()->create(['notificacion_id'=>null]);
        $this->actingAs($fake_user)->put(route('tasks.update', $tarea), [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea con Notificación',
            'user_id'               => 1,
            'periodicidad'          => 10,
            'notif_nombre'          => 'Notificación de prueba',
            'plantilla_id'          => 1,
            'telefono'              => '555 555-555-555',
            'email'                 => 'test@mail.com',
            'notificacion'          => 'a',
            //'notificar_email'       => '1',
            'notificar_sms'         => '1',
        ])->assertRedirect(route('tasks.index'));
        $notificacion = Notificacion::first();
        $this->assertDatabaseHas('tareas', [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea con Notificación',
            'user_id'               => 1,
            'periodicidad'          => 10,
            'notificacion_id'       => $notificacion->id,
        ]);
        $this->assertDatabaseHas('notificaciones', [
            'nombre'            => 'Notificación de prueba',
            'plantilla_id'      => 1,
            'telefono'          => '555 555-555-555',
            'email'             => 'test@mail.com',
            'notificar_email'   => '0',
            'notificar_sms'     => '1',
        ]);
    }

    /** @test **/
    function it_updates_a_task_that_already_has_notification_without_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $notificacion = Notificacion::factory()->create();
        $tarea = Tarea::factory()->create(['notificacion_id'=>$notificacion->id]);
        $this->actingAs($fake_user)->put(route('tasks.update', $tarea), [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea de prueba',
            'user_id'               => 1,
            'periodicidad'          => 10,
        ])->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tareas', ['nombre'=>'Tarea de prueba', 'notificacion_id'=>null]);
        $this->assertEquals(0, Notificacion::count());
    }

    /** @test **/
    function it_updates_a_task_that_already_has_notification_with_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $notificacion = Notificacion::factory()->create();
        $tarea = Tarea::factory()->create(['notificacion_id'=>$notificacion->id]);

        $this->actingAs($fake_user)->put(route('tasks.update', $tarea), [
            'tipo_mantenimiento'    => 'P2',
            'nombre'                => 'Tarea',
            'user_id'               => 1,
            'periodicidad'          => 10,
            'notif_nombre'          => 'Notificación',
            'plantilla_id'          => 1,
            'notificacion'          => 'a',
        ])->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tareas', ['nombre'=>'Tarea', 'notificacion_id'=>$notificacion->id]);
        $this->assertDatabaseHas('notificaciones', ['nombre'=>'Notificación']);
    }

    /** @test **/
    function validate_all_required_fields_when_updating_task()
    {
        $fake_user = $this->getFakeUser();
        
        $tarea = Tarea::factory()->create();
        $nombre = $tarea->nombre;
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->put(route('tasks.update', $tarea), [
                'tipo_mantenimiento'    => '',
                'nombre'                => '',
                'user_id'               => '',
                'periodicidad'          => '',
                'notif_nombre'          => '',
                'plantilla_id'          => '',
                'telefono'              => '',
                'email'                 => '',
                'notificacion'          => 'on',
                'notificar_email'       => '1',
                'notificar_sms'         => '1',
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors([
                'tipo_mantenimiento', 'nombre', 'user_id', 'periodicidad', 'notif_nombre', 'plantilla_id', 'telefono', 'email'
            ], null, 'update');
        $this->assertDatabaseHas('tareas', ['nombre' => $nombre]);
    }

    /** @test **/
    function periodicidad_must_be_numeric_when_updating_task()
    {
        $fake_user = $this->getFakeUser();
        $tarea = Tarea::factory()->create();
        $nombre = $tarea->nombre;
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->put(route('tasks.update', $tarea), [
                'tipo_mantenimiento'    => 'a',
                'nombre'                => 'a',
                'periodicidad'          => 'a',
                'user_id'               => 1,
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors(['periodicidad'], null, 'update');
        $this->assertDatabaseHas('tareas', ['nombre' => $nombre]);
    }

    /** @test **/
    function periodicidad_must_be_multiple_of_5_when_updating_task()
    {
        $fake_user = $this->getFakeUser();
        $tarea = Tarea::factory()->create();
        $nombre = $tarea->nombre;
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->put(route('tasks.update', $tarea), [
                'tipo_mantenimiento'    => 'a',
                'nombre'                => 'a',
                'periodicidad'          => 12,
                'user_id'               => 1,
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors(['periodicidad'], null, 'update');
        $this->assertDatabaseHas('tareas', ['nombre' => $nombre]);
    }

    /** @test **/
    function the_notif_email_must_be_valid_when_updating_task()
    {
        $fake_user = $this->getFakeUser();
        
        $tarea = Tarea::factory()->create();
        $nombre = $tarea->nombre;
        $this->actingAs($fake_user)
            ->from(route('tasks.index'))
            ->put(route('tasks.update', $tarea), [
                'tipo_mantenimiento'    => 'P2',
                'nombre'                => 'Tarea con Notificación',
                'user_id'               => 1,
                'periodicidad'          => 10,
                'notif_nombre'          => 'Notificación de prueba',
                'plantilla_id'          => 1,
                'telefono'              => '555 555-555-555',
                'email'                 => 'non_valid_email',
                'notificacion'          => 'a',
                'notificar_email'       => '1',
                'notificar_sms'         => '1',
            ])
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHasErrors(['email'], null, 'update');
        $this->assertDatabaseHas('tareas', ['nombre' => $nombre]);
        $this->assertEquals(0, Notificacion::count());
    }

    /** @test **/
    function it_deletes_a_task_with_notification()
    {
        $fake_user = $this->getFakeUser();
        
        $notificacion = Notificacion::factory()->create();
        $tarea = Tarea::factory()->create(['notificacion_id' => $notificacion->id]);

        $this->actingAs($fake_user)
            ->delete(route('tasks.delete', $tarea))
            ->assertRedirect(route('tasks.index'));
        $this->assertSame(0, Tarea::count());
        $this->assertSame(0, Notificacion::count());
    }

    /** @test **/
    function doing_the_schedule_routine_with_mails_and_sms()
    {
        $fake_user = $this->getFakeUser();

        Mail::fake();
        
        $cliente = User::factory()->create();
        $plantilla = Plantilla::factory()->create();
        $notificacion = Notificacion::factory()->create([
            'user_id'=>$cliente->id, 'plantilla_id'=>$plantilla->id, 'notificar_email'=>1, 'notificar_sms'=>1,
        ]);
        $tarea = Tarea::factory()->create([
            'user_id'=>$cliente->id, 'notificacion_id' => $notificacion->id, 'periodicidad'=>5
        ]);

        $this->actingAs($fake_user)
            ->get(route('tasks.cron'))
            ->assertStatus(200)
            ->assertSee("Enviando email a")
            ->assertSee($notificacion->email)
            ->assertSee("Enviando SMS a")
            ->assertSee($notificacion->telefono);

        Mail::assertSent(NotificacionMail::class);

        Mail::assertSent(function (NotificacionMail $mail) use ($notificacion) {
            return $mail->plantilla->id === $notificacion->plantilla->id;
        });

        $this->travel(5)->minutes();

        $this->actingAs($fake_user)
            ->get(route('tasks.cron'))
            ->assertStatus(200)
            ->assertSee("Enviando email a")
            ->assertSee($notificacion->email)
            ->assertSee("Enviando SMS a")
            ->assertSee($notificacion->telefono);

        Mail::assertSent(NotificacionMail::class);

        Mail::assertSent(function (NotificacionMail $mail) use ($notificacion) {
            return $mail->plantilla->id === $notificacion->plantilla->id;
        });
    }
}
