<?php

namespace Database\Factories;

use App\Models\GoodReceive;
use App\Models\InboundDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodReceiveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoodReceive::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'inbound_delivery_id' => InboundDelivery::factory(),
            'reference' => 'RCV-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'receive_date' => $this->faker->dateTime(),
            'notes' => $this->faker->optional()->text(),
            'status' => GoodReceive::STATUS_DRAFT
        ];
    }
}
