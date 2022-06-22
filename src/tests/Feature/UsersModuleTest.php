<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Rol;
use App\Models\Empresa;

use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;

class UsersModuleTest extends TestCase
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

    /**
     * A basic feature test example.
     *
     * @return void
     */
    function it_shows_the_user_list_page()
    {
        $fake_user = $this->getFakeUser();
        User::factory()->create(['nombre' => 'Alan']);
        User::factory()->create(['nombre' => 'Laura']);

        $response = $this->actingAs($fake_user)->get('/usuarios');

        $response->assertStatus(200);
        $response->assertViewIs(route('users.index'));
        $response->assertSee('Usuarios');
        $response->assertSee('Alan');
        $response->assertSee('Laura');
    }

    /** @test **/
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $fake_user = $this->getFakeUser();
        $response = $this->actingAs($fake_user)->get('/usuarios');
        $response->assertStatus(200);
        $response->assertSee('No hay usuarios para mostrar por el momento...');
    }

    /** @test **/
    function it_displays_the_user_details()
    {
        $fake_user = $this->getFakeUser();

        $rol = Rol::create(['nombre' => 'Cliente']);
        $empresa = Empresa::factory()->create();
        $user = User::factory()->create(['empresa_id'=>$empresa->id, 'rol_id'=>$rol->id]);

        $response = $this->actingAs($fake_user)
            ->getJson(route('users.show', $user))
            ->assertStatus(200)
            ->assertJson([
                'nombre'=>$user->nombre,
                'empresa' => $empresa->nombre,
                'rol' => $rol->nombre,
            ]);
    }

    /** @test **/
    function it_displays_the_user2_details()
    {
        $fake_user = $this->getFakeUser();

        $rol = Rol::create(['nombre' => 'Cliente']);
        User::factory()->count(49)->create();
        $user = User::factory()->create(['rol_id'=>$rol->id]);

        $response = $this->actingAs($fake_user)
            ->getJson(route('users.show', $user))
            ->assertStatus(200)
            ->assertJson(['nombre'=>$user->nombre]);
    }

    /** @test **/
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->get('/usuarios/1')
            ->assertStatus(404);
    }

    /** @test **/
    function it_creates_a_new_user()
    {
        //$this->withoutExceptionHandling();

        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->post('/usuarios', [
            'nombre' => 'Alan Chávez',
            'cedula' => 123456,
            'email1' => 'alan_chavez@gmail.com',
            'email2' => 'alan_chavez@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 2,
            'rol_id' => 1,
            'password' => '123456',
        ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'nombre' => 'Alan Chávez',
            'cedula' => 123456,
            'email1' => 'alan_chavez@gmail.com',
            'email2' => 'alan_chavez@hotmail.com',
            'direccion' => 'Calle Falsa 123',
            'empresa_id' => 2,
            'rol_id' => 1,
            'password' => '123456',
        ]);
        //$this->assertDatabaseHas('users', [...]);
    }

    /** @test **/
    function validate_all_required_fields_on_user_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from('/usuarios')
            ->post('/usuarios', [
                'nombre' => '',
                'cedula' => '',
                'email1' => '',
                'email2' => '',
                'direccion' => '',
                'empresa_id' => '',
                'rol_id' => '',
                'password' => '',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors([
                'nombre', 'cedula', 'email1', 'email2', 'direccion', 'empresa_id', 'rol_id', 'password'
            ], null, 'store');

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function the_cedula_field_must_be_numeric()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)
            ->from('/usuarios')
            ->post('/usuarios', [
                'nombre' => 'a',
                'cedula' => 'a',
                'email1' => 'a1@a.a',
                'email2' => 'a2@a.a',
                'direccion' => 'a',
                'empresa_id' => 1,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['cedula' ], null, 'store');

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function the_emails_must_be_valid()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Duilio',
                'cedula' => 123456,
                'email1' => 'corre_no_valido1',
                'email2' => 'corre_no_valido2',
                'direccion' => 'Calle Falsa 123',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'store');

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function the_emails_must_be_unique()
    {
        $fake_user = $this->getFakeUser();
        
        User::factory()->create([
            'email1' => 'duilio@styde.net',
            'email2' => 'duilio@hotmail.com',
        ]);
        $this->actingAs($fake_user)
            ->from(route('users.index'))
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
            ->assertSessionHasErrors(['email1','email2'], null, 'store');

        $this->assertEquals(1, User::count());
    }

    /** @test **/
    function the_email1_must_be_unique_from_email2_and_viceversa()
    {
        $fake_user = $this->getFakeUser();
        
        User::factory()->create(['email1'=>'test1@test.test','email2'=>'test2@test.test']);
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Test',
                'cedula' => 111111,
                'email1' => 'test2@test.test',
                'email2' => 'test1@test.test',
                'direccion' => 'Test',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'store');

        $this->assertEquals(1, User::count());
    }

    /** @test **/
    function the_email1_and_email2_fields_must_be_different()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->post('/usuarios', [
                'nombre' => 'Test',
                'cedula' => 111111,
                'email1' => 'test@test.test',
                'email2' => 'test@test.test',
                'direccion' => 'Test',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'store');

        $this->assertEquals(0, User::count());
    }

    /** @test **/
    function it_edits_the_user_details_page()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create(['nombre'=>'Alan Chávez']);
        $this->actingAs($fake_user)
            ->getJson(route('users.edit', ['user'=>$user]))
            ->assertStatus(200)
            //->assertViewHas('user')
            ->assertJson(['nombre'=>'Alan Chávez']);
    }

    /** @test **/
    function it_updates_an_user()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create();
        $this->actingAs($fake_user)->put(route('users.update', $user), [
            'nombre' => 'Duilio',
            'cedula' => 33333,
            'email1' => 'duilio@gmail.com',
            'email2' => 'duilio@hotmail.com',
            'direccion' => 'Test',
            'empresa_id' => 3,
            'rol_id' => 2,
            'password' => '123456',
        ])->assertRedirect(route('users.index'));

        $this->assertCredentials([
            'nombre' => 'Duilio',
            'cedula' => 33333,
            'email1' => 'duilio@gmail.com',
            'email2' => 'duilio@hotmail.com',
            'direccion' => 'Test',
            'empresa_id' => 3,
            'rol_id' => 2,
            'password' => '123456',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_when_updating_an_user()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create();
        $nombre = $user->nombre;
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => '',
                'cedula' => '',
                'email1' => '',
                'email2' => '',
                'direccion' => '',
                'empresa_id' => '',
                'rol_id' => '',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['nombre','cedula','email1','email2','direccion','empresa_id','rol_id'], null, 'update');

        $this->assertDatabaseHas('users', ['nombre' => $nombre]);
    }

    /** @test **/
    function the_cedula_field_must_be_numeric_when_updating_an_user()
    {
        $fake_user = $this->getFakeUser();

        $user = User::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'a',
                'cedula' => 'a',
                'email1' => 'a1@a.a',
                'email2' => 'a2@a.a',
                'direccion' => 'a',
                'empresa_id' => 1,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['cedula' ], null, 'update');

        $this->assertDatabaseMissing('users', ['nombre'=>'a']);
    }

    /** @test **/
    function the_emails_must_be_valid_when_updating_an_user()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'a',
                'cedula' => 123,
                'email1' => 'a',
                'email2' => 'a',
                'direccion' => 'a',
                'empresa_id' => 1,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'update');

        $this->assertDatabaseMissing('users', ['nombre'=>'a']);
    }

    /** @test **/
    function the_emails_must_be_unique_when_updating_an_user()
    {
        $fake_user = $this->getFakeUser();
        
        User::factory()->create([
            'email1' => 'test@gmail.com',
            'email2' => 'test@hotmail.com',
        ]);
        $user = User::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'a',
                'cedula' => 123,
                'email1' => 'test@gmail.com',
                'email2' => 'test@hotmail.com',
                'direccion' => 'a',
                'empresa_id' => 1,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1','email2'], null, 'update');

        $this->assertDatabaseMissing('users', ['nombre'=>'a']);
    }

    /** @test **/
    function the_email1_must_be_unique_from_email2_and_viceversa_when_updating()
    {
        $fake_user = $this->getFakeUser();
        
        User::factory()->create(['email1' => 'test1@test.test', 'email2' => 'test2@test.test']);
        $user = User::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'Test',
                'cedula' => 111111,
                'email1' => 'test2@test.test',
                'email2' => 'test1@test.test',
                'direccion' => 'Test',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'update');

        $this->assertDatabaseMissing('users', ['nombre'=>'Test']);
    }

    /** @test **/
    function user_can_swap_email1_and_email2_when_updating()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create(['email1' => 'test1@test.test', 'email2' => 'test2@test.test']);
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'Test',
                'cedula' => 111111,
                'email1' => 'test2@test.test',
                'email2' => 'test1@test.test',
                'direccion' => 'Test',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'));
        $this->assertCredentials([
            'nombre' => 'Test',
            'cedula' => 111111,
            'email1' => 'test2@test.test',
            'email2' => 'test1@test.test',
            'direccion' => 'Test',
            'empresa_id' => 2,
            'rol_id' => 1,
            'password' => '123456',
        ]);
    }

    /** @test **/
    function the_email1_and_email2_fields_must_be_different_when_updating()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('users.index'))
            ->put(route('users.update', $user), [
                'nombre' => 'Test',
                'cedula' => 111111,
                'email1' => 'test@test.test',
                'email2' => 'test@test.test',
                'direccion' => 'Test',
                'empresa_id' => 2,
                'rol_id' => 1,
                'password' => '123456',
            ])
            ->assertRedirect(route('users.index'))
            ->assertSessionHasErrors(['email1', 'email2'], null, 'update');

        $this->assertDatabaseMissing('users', ['nombre'=>'Test']);
    }

    /** @test **/
    function it_deletes_a_user()
    {
        $fake_user = $this->getFakeUser();
        
        $user = User::factory()->create();

        $this->actingAs($fake_user)
            ->delete(route('users.delete', ['user'=>$user]))
            ->assertRedirect(route('users.index'));
        $this->assertSame(0, User::count());
        $this->assertDatabaseMissing('users', [
                'id' => $user->id,
            ]);
    }
}
