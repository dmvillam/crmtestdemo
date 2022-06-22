<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\ImpersonatesUsers;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Plantilla;

class PlantillasModuleTest extends TestCase
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

    /** @test */
    function it_shows_the_templates_list()
    {
        $fake_user = $this->getFakeUser();
        Plantilla::factory()->create(['nombre' => 'Plantilla 1']);
        Plantilla::factory()->create(['nombre' => 'Plantilla 2']);

        $response = $this->actingAs($fake_user)
            ->get(route('templates.index'))
            ->assertStatus(200)
            ->assertViewIs('templates.index')
            ->assertViewHas('plantillas')
            ->assertSee('Plantillas para notificaciones')
            ->assertSee('Plantilla 1')
            ->assertSee('Plantilla 2')
            ->assertSee('Crear nueva plantilla');
    }

    /** @test **/
    function it_shows_a_default_message_if_the_templates_list_is_empty()
    {
        $fake_user = $this->getFakeUser();
        $response = $this->actingAs($fake_user)->get(route('templates.index'));
        $response->assertStatus(200);
        $response->assertSee('No hay plantillas para mostrar por el momento... Crea la primera');
    }

    /** @test **/
    function it_displays_the_template_details()
    {
        $fake_user = $this->getFakeUser();

        $plantilla = Plantilla::factory()->create();

        $response = $this->actingAs($fake_user)
            ->getJson(route('templates.show', $plantilla))
            ->assertStatus(200)
            ->assertJson([
                'template'=>$plantilla->nombre,
            ]);

        $response->assertSee($plantilla->descripcion_corta);

    }

    /** @test **/
    function it_displays_another_template_details()
    {
        $fake_user = $this->getFakeUser();

        Plantilla::factory()->count(49)->create();
        $plantilla = Plantilla::factory()->create();

        $response = $this->actingAs($fake_user)
            ->getJson(route('templates.show', $plantilla))
            ->assertStatus(200)
            ->assertJson(['template'=>$plantilla->nombre]);
    }

    /** @test **/
    function it_displays_a_404_error_if_the_template_is_not_found()
    {
        $fake_user = $this->getFakeUser();
        $this->actingAs($fake_user)
            ->get(route('templates.show', '1'))
            ->assertStatus(404);
    }

    /** @test **/
    function it_creates_a_new_template()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)->post(route('templates.store'), [
            'nombre'            => 'Plantilla',
            'descripcion_larga' => '<p>Descripción Email</p>',
            'descripcion_corta' => 'Descripción SMS',
        ])->assertRedirect(route('templates.index'));

        $this->assertDatabaseHas('plantillas', [
            'nombre'            => 'Plantilla',
            'descripcion_larga' => '<p>Descripción Email</p>',
            'descripcion_corta' => 'Descripción SMS',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_on_template_storing()
    {
        $fake_user = $this->getFakeUser();
        
        $this->actingAs($fake_user)
            ->from(route('templates.index'))
            ->post(route('templates.store'), [
                'nombre'            => '',
                'descripcion_larga' => '',
                'descripcion_corta' => '',
            ])
            ->assertRedirect(route('templates.index'))
            ->assertSessionHasErrors([
                'nombre', 'descripcion_larga', 'descripcion_corta'
            ], null, 'store');

        $this->assertEquals(0, Plantilla::count());
    }

    /** @test */
    public function it_uploads_an_image_for_the_email_content()
    {
        $fake_user = $this->getFakeUser();
        Storage::fake('uploads');

        $file = UploadedFile::fake()->image('upload.jpg');
 
        $this->actingAs($fake_user)
            ->post(route('templates.upload'), [
                'fileimage' => $file,
            ])
            ->assertStatus(200);
        Storage::disk('uploads')->assertExists($file->hashName());
    }

    /** @test **/
    function it_retrieves_correctly_the_template_editing_info()
    {
        $fake_user = $this->getFakeUser();
        $plantilla = Plantilla::factory()->create(['nombre'=>'Plantilla']);
        $this->actingAs($fake_user)
            ->getJson(route('templates.edit', $plantilla))
            ->assertStatus(200)
            ->assertJson(['nombre'=>'Plantilla']);
    }

    /** @test **/
    function it_updates_the_template()
    {
        $fake_user = $this->getFakeUser();
        $plantilla = Plantilla::factory()->create();
        $this->actingAs($fake_user)->put(route('templates.update', $plantilla), [
            'nombre'            => 'Plantilla de prueba',
            'descripcion_larga' => '<p>Descripción Email</p>',
            'descripcion_corta' => 'Descripción SMS',
        ])->assertRedirect(route('templates.index'));
        $this->assertDatabaseHas('plantillas', [
            'nombre'            => 'Plantilla de prueba',
            'descripcion_larga' => '<p>Descripción Email</p>',
            'descripcion_corta' => 'Descripción SMS',
        ]);
    }

    /** @test **/
    function validate_all_required_fields_when_updating_task()
    {
        $fake_user = $this->getFakeUser();
        $plantilla = Plantilla::factory()->create();
        $nombre = $plantilla->nombre;
        $this->actingAs($fake_user)
            ->from(route('templates.index'))
            ->put(route('templates.update', $plantilla), [
                'nombre'            => '',
                'descripcion_larga' => '',
                'descripcion_corta' => '',
            ])
            ->assertRedirect(route('templates.index'))
            ->assertSessionHasErrors([
                'nombre', 'descripcion_larga', 'descripcion_corta'
            ], null, 'update');
        $this->assertDatabaseHas('plantillas', ['nombre' => $nombre]);
    }

    /** @test **/
    function it_deletes_a_template()
    {
        $fake_user = $this->getFakeUser();
        $plantilla = Plantilla::factory()->create();
        $this->actingAs($fake_user)
            ->delete(route('templates.delete', $plantilla))
            ->assertRedirect(route('templates.index'));
        $this->assertSame(0, Plantilla::count());
    }
}
