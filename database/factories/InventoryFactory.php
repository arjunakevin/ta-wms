<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'location_id' => Location::factory(),
            'documentable_id' => null,
            'documentable_type' => null,
            'product_id' => Product::factory(),
            'base_quantity' => $this->faker->randomNumber(3),
            'pick_quantity' => $this->faker->randomNumber(3),
            'put_quantity' => $this->faker->randomNumber(3),
            'posting_date' => $this->faker->dateTime(),
        ];
    }
}
