<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true), // Generates 3 random words for product name
            'description' => $this->faker->paragraph(), // Generates a paragraph for description
            'price' => $this->faker->randomFloat(2, 1, 1000), // Generates a float price between 1 and 1000 with 2 decimal places
            'stock' => $this->faker->numberBetween(0, 500), // Generates an integer stock between 0 and 500
        ];
    }
}
