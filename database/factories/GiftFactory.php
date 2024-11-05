<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gift>
 */
class GiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $price = random_int(20, 200) * (random_int(0, 3) ? 1 : -1);

        return [
            'name' => fake()->sentence(random_int(1, 4)),
            'description' => fake()->text(),
            'relative_probability' => random_int(0, 1000) / 10,
            'price' => $price,
            'sell_price' => $price < 0 ? 0 : floor($price * (random_int(50, 75) / 100)),
        ];
    }
}
