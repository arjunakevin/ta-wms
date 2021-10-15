<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\InboundDelivery;
use App\Models\InboundDeliveryDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class InboundDeliveryDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InboundDeliveryDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'inbound_delivery_id' => InboundDelivery::factory(),
            'product_id' => Product::factory(),
            'line_id' => $this->faker->randomNumber(3),
            'base_quantity' => $this->faker->randomNumber(3),
            'open_quantity' => $this->faker->randomNumber(3),
        ];
    }
}
