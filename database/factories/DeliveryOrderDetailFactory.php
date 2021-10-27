<?php

namespace Database\Factories;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\OutboundDeliveryDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryOrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeliveryOrderDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'delivery_order_id' => DeliveryOrder::factory(),
            'outbound_detail_id' => OutboundDeliveryDetail::factory(),
            'base_quantity' => $this->faker->randomNumber(3),
            'good_issue_quantity' => $this->faker->randomNumber(3),
            'open_check_quantity' => $this->faker->randomNumber(3)
        ];
    }
}
