<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rol;
use App\Models\Empresa;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    function it_shows_the_user_list_page()
    {
        User::factory()->create(['nombre' => 'Alan']);
        User::factory()->create(['nombre' => 'Laura']);

        $response = $this->get('/usuarios');

        $response->assertStatus(200);
        $response->assertViewIs(route('users.index'));
        $response->assertSee('Usuarios');
        $response->assertSee('Alan');
        $response->assertSee('Laura');
    }

    /** @test **/
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $response = $this->get('/usuarios');
        $response->assertStatus(200);
        $response->assertSee('No hay usuarios para mostrar por el momento...');
    }

    /** @test **/
    function it_displays_the_user_details()
    {
        $rol = Rol::create(['nombre' => 'Cliente']);
        $empresa = Empresa::factory()->create();
        $user = User::factory()->create([
            'nombre'=>'Alan Chávez',
            'rol_id' => $rol->id,
            'empresa_id' => $empresa->id,
        ]);

        $response = $this->getJson("/usuarios/{$user->id}");
        $response->assertStatus(200)
            ->assertJson([
                'nombre'=>'Alan Chávez',
                'empresa' => $empresa->nombre,
                'rol' => $rol->nombre,
            ]);
    }

    /** @test **/
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->get('/usuarios/1')
            ->assertStatus(404);
    }

    /** @test **/
    function it_loads_the_new_user_page()
    {
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear nuevo usuario');
    }

    /** @test **/
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios', [
            'nombre' => 'Alan Chávez',
            'cedula' => 123456,
            'email1' => 'alan_chavez@gmail.com',
            'email2' => 'alan_chavez@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 2,
            'rol_id' => 1,
            //'password' => '123456',
        ])->assertRedirect(route('users.index'));

        // assertCredentials is for checking that the password has encrypted correctly
        /*$this->assertCredentials([
            'nombre' => 'Alan Chávez',
            'cedula' => 123456,
            'email1' => 'alan_chavez@gmail.com',
            'email2' => 'alan_chavez@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 2,
            'rol_id' => 1,
            'password' => '123456',
        ]);*/
        $this->assertDatabaseHas('users', [
            'nombre' => 'Alan Chávez',
            'cedula' => 123456,
            'email1' => 'alan_chavez@gmail.com',
            'email2' => 'alan_chavez@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 2,
            'rol_id' => 1,
            //'password' => '123456',
        ]);
    }

    /** @test **/
    function the_name_is_required()
    {
        $this->from('/usuarios')
            ->post('/usuarios', [
                'nombre' => '',
                'cedula' => 123456,
                'email1' => 'alan_chavez@gmail.com',
                'email2' => 'alan_chavez@hotmail.com',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['nombre']);

        $this->assertEquals(0, User::count());
        //$this->assertDatabaseMissing('users', [
        //    'email1' => 'alan_chavez@gmail.com',
        //    'email2' => 'alan_chavez@hotmail.com',
        //]);
    }

    /** @test **/
    function the_email1_is_required()
    {
        $this->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Duilio',
                'cedula' => 123456,
                'email1' => '',
                'email2' => 'duilio@mail.net',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1']);

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function the_email1_must_be_valid()
    {
        $this->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Duilio',
                'cedula' => 123456,
                'email1' => 'corre_no_valido',
                'email2' => 'duilio@hotmail.com',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1']);

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function the_email1_must_be_unique()
    {
        User::factory()->create([
            'email1' => 'duilio@styde.net',
        ]);
        $this->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Duilio',
                'cedula' => 123456,
                'email1' => 'duilio@styde.net',
                'email2' => 'duilio@hotmail.com',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1']);

        $this->assertEquals(1, User::count());
    }

    /** @test **/
    function it_edits_the_user_details_page()
    {
        $user = User::factory()->create(['nombre'=>'Alan Chávez']);
        $this->getJson(route('users.edit', ['user'=>$user]))
            ->assertStatus(200)
            //->assertViewHas('user')
            ->assertJson(['nombre'=>'Alan Chávez']);
    }

    /** @test **/
    function it_updates_an_user()
    {
        $user = User::factory()->create();
        $this->put(route('users.update', ['user'=>$user]), [
            'nombre' => 'Duilio',
            'cedula' => 33333,
            'email1' => 'duilio@gmail.com',
            'email2' => 'duilio@hotmail.com',
            'direccion' => 'Test',
            'empresa_id' => 3,
            'rol_id' => 2,
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'nombre' => 'Duilio',
            'cedula' => 33333,
            'email1' => 'duilio@gmail.com',
            'email2' => 'duilio@hotmail.com',
            'direccion' => 'Test',
            'empresa_id' => 3,
            'rol_id' => 2,
        ]);
    }

    /** @test **/
    function the_name_is_required_when_updating_an_user()
    {
        $user = User::factory()->create();
        $this->from(route('users.index'))
            ->put(route('users.update', ['user'=>$user]), [
                'nombre' => '',
                'cedula' => 123456,
                'email1' => 'duilio@gmail.com',
                'email2' => 'duilio@hotmail.com',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['nombre']);

        $this->assertDatabaseMissing('users', ['email1'=>'duilio@gmail.com']);
    }

    /** @test **/
    function it_deletes_a_user()
    {
        $user = User::factory()->create();

        $this->delete(route('users.delete', ['user'=>$user]))
            ->assertRedirect(route('users.index'));
        $this->assertSame(0, User::count());
        $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);
    }
}
