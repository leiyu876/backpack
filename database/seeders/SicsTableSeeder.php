<?php

namespace Database\Seeders;

use App\Models\Sic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            [
                'code' => 111,
                'description' => 'Wheat',
            ],
            [
                'code' => 112,
                'description' => 'Rice',
            ],
            [
                'code' => 113,
                'description' => 'Corn',
            ],
        );

        DB::table('sics')->delete();

        foreach ($items as $item) {
            
            Sic::create([
                'code' => $item['code'],    
                'description' => $item['description'],    
            ]);
        }
    }
}
