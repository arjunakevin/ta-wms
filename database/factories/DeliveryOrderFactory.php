<?php

namespace Database\Factories;

use App\Models\DeliveryOrder;
use App\Models\OutboundDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DeliveryOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'outbound_delivery_id' => OutboundDelivery::factory(),
            'reference' => 'DVO-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'good_issue_date' => $this->faker->optional()->dateTime(),
            'notes' => $this->faker->optional()->text(),
            'status' => DeliveryOrder::STATUS_UNALLOCATED
        ];
    }
}
