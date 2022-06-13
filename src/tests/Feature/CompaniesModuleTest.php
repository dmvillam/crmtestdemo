<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;

use App\Models\User;
use App\Models\Empresa;

class CompaniesModuleTest extends TestCase
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
    function it_shows_the_companies_list_page()
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

    /** @test **/
    function it_shows_a_default_message_if_the_companies_list_is_empty()
    {
        $fake_user = $this->getFakeUser();
        $response = $this->actingAs($fake_user)->get(route('companies.index'));
        $response->assertStatus(200);
        $response->assertSee('No hay empresas para mostrar por el momento...');
    }

    /** @test **/
    function it_displays_the_company_details()
    {
        $fake_user = $this->getFakeUser();

        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($fake_user)
            ->getJson(route('companies.show', $empresa))
            ->assertStatus(200)
            ->assertJson([
                'company'=>$empresa->nombre,
            ]);

        $response = $this->actingAs($fake_user)
            ->get(route('companies.show', $empresa))
            ->assertStatus(200)
            ->assertSee($empresa->cedula_juridica)
            ->assertSee($empresa->telefono)
            ->assertSee($empresa->correo)
            ->assertSee(str_replace("\n", '\n', $empresa->direccion));
    }

    /** @test **/
    function it_displays_the_company2_details()
    {
        $fake_user = $this->getFakeUser();

        Empresa::factory()->count(49)->create();
        $empresa = Empresa::factory()->create();

        $response = $this->actingAs($fake_user)
            ->getJson(route('companies.show', $empresa))
            ->assertStatus(200)
            ->assertJson(['company'=>$empresa->nombre]);
    }

    /** @test **/
    function it_displays_a_404_error_if_the_company_is_not_found()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)
            ->get(route('companies.show', '1'))
            ->assertStatus(404);
    }

    /** @test **/
    function it_loads_the_new_company_page()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)
            ->get(route('companies.index'))
            ->assertStatus(200)
            ->assertSee('Crear nueva empresa');
    }

    /** @test **/
    function it_creates_a_new_company()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->post(route('companies.store'), [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
            'logo'              => 'a',
            'direccion'         => 'a',
        ])->assertRedirect(route('companies.index'));

        $this->assertDatabaseHas('empresas', [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
            'logo'              => 'a',
            'direccion'         => 'a',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_on_company_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->post(route('companies.store'), [
                'cedula_juridica'   => '',
                'nombre'            => '',
                'telefono'          => '',
                'email'             => '',
                'direccion'         => '',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors([
                'cedula_juridica', 'nombre', 'telefono', 'email', 'direccion'
            ], null, 'store');

        $this->assertEquals(0, Empresa::count());
    }

    /** @test **/
    function the_company_email_must_be_valid_on_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->post(route('companies.store'), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'not_valid_email',
                'direccion'         => 'a',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['email'], null, 'store');

        $this->assertEquals(0, Empresa::count());
    }

    /** @test **/
    function the_company_email_must_be_unique_on_storing()
    {
        $fake_user = $this->getFakeUser();
        
        Empresa::factory()->create(['email' => 'company@mail.net']);
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->post(route('companies.store'), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'company@mail.net',
                'direccion'         => 'a',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['email'], null, 'store');

        $this->assertEquals(1, Empresa::count());
        $this->assertDatabaseMissing('empresas', ['nombre'=>'a']);
    }

    /** @test **/
    function it_retrieves_correctly_the_company_editing_info()
    {
        $fake_user = $this->getFakeUser();
        $empresa = Empresa::factory()->create(['nombre'=>'TestCompany']);
        $this->actingAs($fake_user)
            ->getJson(route('companies.edit', ['empresa'=>$empresa]))
            ->assertStatus(200)
            ->assertJson(['nombre'=>'TestCompany']);
    }

    /** @test **/
    function it_updates_a_company()
    {
        $fake_user = $this->getFakeUser();
        
        $empresa = Empresa::factory()->create();
        $this->actingAs($fake_user)->put(route('companies.update', $empresa), [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
            'logo'              => 'a',
            'direccion'         => 'a',
        ])->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('empresas', [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
            'logo'              => 'a',
            'direccion'         => 'a',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_when_updating_a_company()
    {
        $fake_user = $this->getFakeUser();
        
        $empresa = Empresa::factory()->create();
        $nombre = $empresa->nombre;
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => '',
                'nombre'            => '',
                'telefono'          => '',
                'email'             => '',
                'direccion'         => '',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['cedula_juridica','nombre','telefono','email','direccion'], null, 'update');

        $this->assertDatabaseHas('empresas', ['nombre' => $nombre]);
    }

    /** @test **/
    function the_email_must_be_valid_when_updating_a_company()
    {
        $fake_user = $this->getFakeUser();
        
        $empresa = Empresa::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'non_valid_email',
                'direccion'         => 'a',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['email'], null, 'update');

        $this->assertDatabaseMissing('empresas', ['nombre'=>'a']);
    }

    /** @test **/
    function the_email_must_be_unique_when_updating_a_company()
    {
        $fake_user = $this->getFakeUser();
        
        Empresa::factory()->create(['email' => 'test@mail.com']);
        $empresa = Empresa::factory()->create();
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'test@mail.com',
                'direccion'         => 'a',
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['email'], null, 'update');

        $this->assertDatabaseMissing('empresas', ['nombre'=>'a']);
    }

    /** @test **/
    function it_deletes_a_company()
    {
        $fake_user = $this->getFakeUser();
        
        $empresa = Empresa::factory()->create();

        $this->actingAs($fake_user)
            ->delete(route('companies.delete', $empresa))
            ->assertRedirect(route('companies.index'));
        $this->assertSame(0, Empresa::count());
        $this->assertDatabaseMissing('empresas', [
            'id' => $empresa->id,
        ]);
    }
}