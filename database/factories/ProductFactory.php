<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => Client::factory(),
            'code' => 'PRD-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'barcode' => $this->faker->unique()->randomNumber(8),
            'description_1' => $this->faker->text(),
            'description_2' => $this->faker->optional()->text(),
            'uom_name' => strtoupper($this->faker->word()),
            'is_active' => $this->faker->boolean()
        ];
    }
}
