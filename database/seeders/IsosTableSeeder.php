<?php

namespace Database\Seeders;

use App\Models\Iso;
use Illuminate\Database\Seeder;

class IsosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Iso::factory()
            ->count(3)
            ->create();
    }
}
