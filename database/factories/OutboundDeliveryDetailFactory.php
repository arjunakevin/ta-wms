<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\OutboundDelivery;
use App\Models\OutboundDeliveryDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutboundDeliveryDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OutboundDeliveryDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'outbound_delivery_id' => OutboundDelivery::factory(),
            'product_id' => Product::factory(),
            'line_id' => $this->faker->randomNumber(3),
            'base_quantity' => $this->faker->randomNumber(3),
            'open_quantity' => $this->faker->randomNumber(3)
        ];
    }
}
