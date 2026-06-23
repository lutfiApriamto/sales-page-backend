<?php
// salesPagesFactory
namespace Database\Factories;

use App\Models\SalesPage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesPageFactory extends Factory
{
    protected $model = SalesPage::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'features' => ['fitur a', 'fitur b'],
            'target_audience' => $this->faker->word(),
            'price' => 'Rp 100.000',
            'unique_selling_points' => $this->faker->sentence(),
            'tone' => 'professional',
            'color_scheme' => 'blue',
            'image_url' => null,
            'logo_url' => null,
            'ai_generated_content' => '<section>contoh</section>',
        ];
    }
}
