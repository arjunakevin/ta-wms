<?php

namespace Database\Factories;

use App\Models\GoodReceive;
use App\Models\MovementOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementOrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MovementOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'reference' => 'MVT-' . strtoupper($this->faker->lexify('??????')) . '-' . $this->faker->randomNumber(6),
            'documentable_id' => GoodReceive::factory(),
            'documentable_type' => get_class(new GoodReceive()),
            'date' => $this->faker->dateTime()->format('Y-m-d H:i:s')
        ];
    }
}
