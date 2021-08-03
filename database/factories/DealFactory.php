<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Deal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $account = Account::inRandomOrder()->first();

        if(!$account) dd('Account table must seed first');

        return [
            'submission_date' => $this->faker->date(),
            'account_id' => mt_rand(1, 3),
            'deal_name' => $account->business_name .' '.$account->deal_indicator,
            'iso_id' => mt_rand(1, 3),
            'sales_stage' => array_rand(config('constants.sales_stages'), 1),
        ];

        $account->increment('deal_indicator');
    }
}
