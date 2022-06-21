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
}
