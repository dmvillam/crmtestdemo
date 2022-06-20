<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
            ->assertViewIs(route('companies.index'))
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
            'direccion'         => 'a',
        ])->assertRedirect(route('companies.index'));

        $this->assertDatabaseHas('empresas', [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
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
    public function logos_can_be_uploaded()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');
 
        $file = UploadedFile::fake()->image('logo.jpg');
 
        $this->actingAs($fake_user)
            ->post(route('companies.store'), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file,
            ])
            ->assertRedirect(route('companies.index'));

        $this->assertDatabaseHas('empresas', ['nombre' => 'a', 'logo' => $file->hashName()]);
        Storage::disk('logos')->assertExists($file->hashName());
    }

    /** @test **/
    public function logo_must_be_an_image()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');
 
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
 
        $this->actingAs($fake_user)
            ->post(route('companies.store'), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file,
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['logo'], null, 'store');

        $this->assertEquals(0, Empresa::count());
        $this->assertDatabaseMissing('empresas', ['nombre'=>'a', 'logo' => $file->hashName()]);
        Storage::disk('logos')->assertMissing($file->hashName());
    }

    /** @test **/
    public function logo_cant_be_long_in_size()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');
 
        $file = UploadedFile::fake()->image('logo.jpg')->size(3000);
 
        $this->actingAs($fake_user)
            ->post(route('companies.store'), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file,
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['logo'], null, 'store');

        $this->assertEquals(0, Empresa::count());
        $this->assertDatabaseMissing('empresas', ['nombre'=>'a', 'logo' => $file->hashName()]);
        Storage::disk('logos')->assertMissing($file->hashName());
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
            'direccion'         => 'a',
        ])->assertRedirect(route('companies.index'));
        $this->assertDatabaseHas('empresas', [
            'cedula_juridica'   => 'a',
            'nombre'            => 'a',
            'telefono'          => '1',
            'email'             => 'a@a.a',
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
    public function logos_can_be_uploaded_when_updating_a_company()
    {
        Storage::fake('logos');
        $fake_user = $this->getFakeUser();

        // Creating the first logo
        $file1 = UploadedFile::fake()->image('logo.jpg');
        $file1_name = $file1->store('/', 'logos');
        $empresa = Empresa::factory()->create(['logo' => $file1_name]);

        // Changing the logo
        $file2 = UploadedFile::fake()->image('logo2.jpg');
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file2,
            ])
            ->assertRedirect(route('companies.index'));
 
        $this->assertDatabaseHas('empresas', ['nombre' => 'a', 'logo' => $file2->hashName()]);
        Storage::disk('logos')->assertExists($file2->hashName());
        Storage::disk('logos')->assertMissing($file1_name);
        
        // We can't compare the original uploaded file with the stored file, because stored file has passed through an image conversion process
        //$this->assertFileEquals($file2, Storage::disk('logos')->path($file2->hashName()));
    }

    /** @test **/
    public function logo_must_be_an_image_when_updating_a_company()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');

        // Creating the first logo
        $file1 = UploadedFile::fake()->image('logo.jpg');
        $file1_name = $file1->store('/', 'logos');
        $empresa = Empresa::factory()->create(['logo' => $file1_name]);
 
        // Changing the logo
        $file2 = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file2,
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['logo'], null, 'update');

        $this->assertDatabaseMissing('empresas', ['nombre'=>'a', 'logo' => $file2->hashName()]);
        Storage::disk('logos')->assertExists($file1_name);
        Storage::disk('logos')->assertMissing($file2->hashName());
    }

    /** @test **/
    public function logo_cant_be_long_in_size_when_updating_a_company()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');

        // Creating the first logo
        $file1 = UploadedFile::fake()->image('logo.jpg');
        $file1_name = $file1->store('/', 'logos');
        $empresa = Empresa::factory()->create(['logo' => $file1_name]);
 
        // Changing the logo
        $file2 = UploadedFile::fake()->image('logo.jpg')->size(3000);
        $this->actingAs($fake_user)
            ->from(route('companies.index'))
            ->put(route('companies.update', $empresa), [
                'cedula_juridica'   => 'a',
                'nombre'            => 'a',
                'telefono'          => '1',
                'email'             => 'a@a.a',
                'direccion'         => 'a',
                'logo'              => $file2,
            ])
            ->assertRedirect(route('companies.index'))
            ->assertSessionHasErrors(['logo'], null, 'update');

        $this->assertDatabaseMissing('empresas', ['nombre'=>'a', 'logo' => $file2->hashName()]);
        Storage::disk('logos')->assertExists($file1_name);
        Storage::disk('logos')->assertMissing($file2->hashName());
    }

    /** @test **/
    function it_deletes_a_company()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('logos');
        
        $file = UploadedFile::fake()->image('logo.jpg');
        $filename = $file->store('/', 'logos');
        $empresa = Empresa::factory()->create(['logo' => $filename]);

        $this->actingAs($fake_user)
            ->delete(route('companies.delete', $empresa))
            ->assertRedirect(route('companies.index'));
        $this->assertSame(0, Empresa::count());
        Storage::disk('logos')->assertMissing($filename);
    }
}
