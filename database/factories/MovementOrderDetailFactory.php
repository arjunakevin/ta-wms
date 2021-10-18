<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Location;
use App\Models\Inventory;
use App\Models\MovementOrder;
use App\Models\MovementOrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementOrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MovementOrderDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'movement_order_id' => MovementOrder::factory(),
            'product_id' => Product::factory(),
            'source_inventory_id' => Inventory::factory(),
            'source_location_id' => Location::factory(),
            'destination_inventory_id' => Inventory::factory(),
            'destination_location_id' => Location::factory(),
            'is_pick' => false,
            'base_quantity' => $this->faker->randomNumber(3),
            'status' => MovementOrderDetail::STATUS_OPEN
        ];
    }
}
