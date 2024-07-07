<?php

namespace Database\Seeders;

use App\Models\RoomRange;
use Illuminate\Database\Seeder;
use App\Constants\HostelConstants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomRangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $ranges = $this->roomRanges();
        foreach ($ranges as $range) {
            RoomRange::create($range);
        }
    }

    private function roomRanges()
    {
        $data = [

            [
                'first_room' => 101,
                'last_room' => 115,
                'floor' => HostelConstants::GROUND_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 116,
                'last_room' => 130,
                'floor' => HostelConstants::GROUND_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],
            [
                'first_room' => 131,
                'last_room' => 145,
                'floor' => HostelConstants::GROUND_FLOOR,
                'girls_wing' => 'Right',
                'boys_wing' => 'Left',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 146,
                'last_room' => 160,
                'floor' => HostelConstants::GROUND_FLOOR,
                'girls_wing' => 'Right',
                'boys_wing' => 'Left',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],
            [
                'first_room' => 201,
                'last_room' => 215,
                'floor' => HostelConstants::FIRST_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 216,
                'last_room' => 230,
                'floor' => HostelConstants::FIRST_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],
            [
                'first_room' => 231,
                'last_room' => 245,
                'floor' => HostelConstants::FIRST_FLOOR,
                'girls_wing' => 'Right',
                'boys_wing' => 'Left',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 246,
                'last_room' => 260,
                'floor' => HostelConstants::FIRST_FLOOR,
                'girls_wing' => 'Right',
                'boys_wing' => 'Left',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],
            [
                'first_room' => 301,
                'last_room' => 315,
                'floor' => HostelConstants::SECOND_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 316,
                'last_room' => 330,
                'floor' => HostelConstants::SECOND_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],
            [
                'first_room' => 331,
                'last_room' => 345,
                'floor' => HostelConstants::SECOND_FLOOR,
                'girls_wing' => 'Right',
                'boys_wing' => 'Left',
                'girls_floor_side' => 'Left',
                'boys_floor_side' => 'Right',
            ],
            [
                'first_room' => 346,
                'last_room' => 360,
                'floor' => HostelConstants::SECOND_FLOOR,
                'girls_wing' => 'Left',
                'boys_wing' => 'Right',
                'girls_floor_side' => 'Right',
                'boys_floor_side' => 'Left',
            ],

        ];

        return $data;
    }
}
