<?php

namespace Database\Seeders;

use App\Models\CheckInOut;
use App\Models\Residence;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CheckInOutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $checkInOut =
            [
                [
                    'type' => 'checkIn',
                    'con_students_date' => '2024-04-28',
                    'block_students_date' => '2024-04-01'
                ],
                [
                    'type' => 'checkOut',
                    'con_students_date' => '2024-06-07',
                    'block_students_date' => '2024-04-26',
                ]
            ];


        foreach ($checkInOut as $record) {
            CheckInOut::create($record);
        }
    }
}
