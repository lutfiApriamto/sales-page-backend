<?php

namespace Tests\Feature;

use App\Models\SalesPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SalesPageStreamTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGeminiStream(string $text): void
    {
        // streamGenerateContent (alt=sse) mengembalikan baris-baris "data: {json}".
        $body = 'data: ' . json_encode([
            'candidates' => [[
                'content' => ['parts' => [['text' => $text]]],
            ]],
        ]) . "\n\n";

        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response($body, 200),
        ]);
    }

    public function test_stream_generates_and_persists_and_decrements_credit(): void
    {
        $user = User::factory()->create(['credits' => 5]);
        Sanctum::actingAs($user);
        $this->fakeGeminiStream('<section>Sales Page Hebat</section>');

        $response = $this->postJson('/api/sales-pages/stream', [
            'product_name' => 'Produk A',
            'description' => 'Deskripsi produk A',
            'tone' => 'professional',
            'color_scheme' => 'blue',
        ]);

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('Sales Page Hebat', $content);
        $this->assertStringContainsString('"done":true', $content);

        $this->assertDatabaseHas('sales_pages', [
            'user_id' => $user->id,
            'product_name' => 'Produk A',
        ]);
        $this->assertEquals(4, $user->fresh()->credits);
    }

    public function test_stream_blocked_when_no_credits(): void
    {
        $user = User::factory()->create(['credits' => 0]);
        Sanctum::actingAs($user);
        Http::fake(); // tidak boleh terpanggil

        $response = $this->postJson('/api/sales-pages/stream', [
            'product_name' => 'Produk A',
            'description' => 'Deskripsi',
            'tone' => 'professional',
            'color_scheme' => 'blue',
        ]);

        $response->assertStatus(403);
        Http::assertNothingSent();
        $this->assertDatabaseCount('sales_pages', 0);
    }

    public function test_stream_refunds_credit_on_gemini_error(): void
    {
        $user = User::factory()->create(['credits' => 5]);
        Sanctum::actingAs($user);
        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response('error', 500),
        ]);

        $response = $this->postJson('/api/sales-pages/stream', [
            'product_name' => 'Produk A',
            'description' => 'Deskripsi',
            'tone' => 'professional',
            'color_scheme' => 'blue',
        ]);

        $response->assertStatus(200);
        $content = $response->streamedContent();

        $this->assertStringContainsString('"error"', $content);
        $this->assertDatabaseCount('sales_pages', 0);
        $this->assertEquals(5, $user->fresh()->credits);
    }

    public function test_stream_requires_authentication(): void
    {
        $response = $this->postJson('/api/sales-pages/stream', [
            'product_name' => 'P',
            'description' => 'D',
        ]);

        $response->assertStatus(401);
    }
}
