<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_image(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Http::fake([
            '*/storage/v1/object/*' => Http::response(['Key' => 'ok'], 200),
        ]);

        $response = $this->postJson('/api/upload', [
            'file' => UploadedFile::fake()->image('produk.png', 600, 400),
            'type' => 'product',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('type', 'product');
        $this->assertStringContainsString('/storage/v1/object/public/', $response->json('url'));
    }

    public function test_upload_rejects_non_image(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/upload', [
            'file' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            'type' => 'product',
        ]);

        $response->assertStatus(422);
    }

    public function test_upload_requires_authentication(): void
    {
        $response = $this->postJson('/api/upload', [
            'file' => UploadedFile::fake()->image('x.png'),
            'type' => 'logo',
        ]);

        $response->assertStatus(401);
    }
}
