<?php

namespace Tests\Unit;

use App\Services\PromptBuilder;
use PHPUnit\Framework\TestCase;

class PromptBuilderTest extends TestCase
{
    public function test_includes_product_data_and_tone(): void
    {
        $prompt = (new PromptBuilder())->build([
            'product_name' => 'Sepatu Lari X',
            'description' => 'Sepatu ringan',
            'features' => ['ringan', 'awet'],
            'target_audience' => 'pelari',
            'price' => 'Rp 500.000',
            'unique_selling_points' => 'paling ringan',
            'tone' => 'aggressive',
            'color_scheme' => 'dark',
            'sections' => ['faq' => true, 'guarantee' => false],
        ]);

        $this->assertStringContainsString('Sepatu Lari X', $prompt);
        $this->assertStringContainsString('ringan, awet', $prompt);
        $this->assertStringContainsString('FAQ', $prompt);
        // tone aggressive harus memengaruhi instruksi gaya
        $this->assertStringContainsString('agresif', strtolower($prompt));
    }

    public function test_embeds_image_and_logo_when_present(): void
    {
        $prompt = (new PromptBuilder())->build([
            'product_name' => 'P',
            'description' => 'D',
            'tone' => 'professional',
            'color_scheme' => 'blue',
            'image_url' => 'https://x/img.png',
            'logo_url' => 'https://x/logo.png',
        ]);

        $this->assertStringContainsString('https://x/img.png', $prompt);
        $this->assertStringContainsString('https://x/logo.png', $prompt);
    }

    public function test_custom_color_used_when_scheme_custom(): void
    {
        $prompt = (new PromptBuilder())->build([
            'product_name' => 'P',
            'description' => 'D',
            'tone' => 'casual',
            'color_scheme' => 'custom',
            'custom_color' => '#ff0066',
        ]);

        $this->assertStringContainsString('#ff0066', $prompt);
    }
}
