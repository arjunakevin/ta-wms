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
        $uoms = ['PCS', 'BOX'];

        return [
            'client_id' => Client::factory(),
            'code' => $this->faker->unique()->numerify('########'),
            'barcode' => $this->faker->optional()->randomNumber(8),
            'description_1' => $this->faker->text(),
            'description_2' => $this->faker->optional()->text(),
            'uom_name' => $this->faker->randomElement($uoms),
            'is_active' => $this->faker->boolean()
        ];
    }
}
