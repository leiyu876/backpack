<?php

namespace Database\Factories;

use App\Models\Iso;
use Illuminate\Database\Eloquent\Factories\Factory;

class IsoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Iso::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'business_name' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'contact_number' => $this->faker->phoneNumber(),
            'emails' => json_encode([
                [
                    'email' => $this->faker->safeEmail()
                ]
            ]),
        ];
    }
}
