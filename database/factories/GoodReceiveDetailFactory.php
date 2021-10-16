<?php

namespace Database\Factories;

use App\Models\GoodReceive;
use App\Models\GoodReceiveDetail;
use App\Models\InboundDeliveryDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodReceiveDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoodReceiveDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'good_receive_id' => GoodReceive::factory(),
            'inbound_detail_id' => InboundDeliveryDetail::factory(),
            'base_quantity' => $this->faker->randomNumber(3),
            'receive_quantity' => $this->faker->randomNumber(3),
            'open_check_quantity' => $this->faker->randomNumber(3),
            'open_putaway_quantity' => $this->faker->randomNumber(3)
        ];
    }
}
