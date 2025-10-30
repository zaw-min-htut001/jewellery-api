<?php

namespace Database\Factories;

use App\Models\Variant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variant>
 */
class VariantFactory extends Factory
{
    protected $model = Variant::class;

    public function definition(): array
    {
        return [
            'carat' => $this->faker->randomElement(['14K', '18K', '22K']),
            'metal_type' => $this->faker->randomElement(['gold', 'white_gold', 'platinum']),
            'price' => $this->faker->randomFloat(2, 1000, 5000),
            'stock' => $this->faker->numberBetween(1, 10),
            'sku' => strtoupper($this->faker->bothify('ITEM-####-??')),
        ];
    }
}
