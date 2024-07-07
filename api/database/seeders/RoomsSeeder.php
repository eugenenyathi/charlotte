<?php

namespace Database\Seeders;

use App\Models\BoysHostel;
use App\Models\GirlsHostel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 101; $i <= 160; $i++) {
            GirlsHostel::create([
                'room' => $i
            ]);


            BoysHostel::create([
                'room' => $i
            ]);
        }

        for ($i = 201; $i <= 260; $i++) {
            GirlsHostel::create([
                'room' => $i
            ]);


            BoysHostel::create([
                'room' => $i
            ]);
        }

        for ($i = 301; $i <= 360; $i++) {
            GirlsHostel::create([
                'room' => $i
            ]);


            BoysHostel::create([
                'room' => $i
            ]);
        }
    }
}
