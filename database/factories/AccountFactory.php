<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'business_name' => $this->faker->unique()->company(),
            'sic_id' => mt_rand(1, 3),
            'owners' => json_encode([
                    [
                        'owner_name' => $this->faker->name(),
                        'title' => $this->faker->title(),
                        'date_of_birth' => $this->faker->date(),
                    ]
                ]),
        ];
    }
}
