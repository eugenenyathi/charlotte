<?php

namespace Database\Seeders;

use App\Constants\StudentConstants;
use App\Models\HostelFees;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HostelFeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hostelFees = [
            [
                'student_type' => StudentConstants::BLOCK_STUDENT,
                'fee' => 60
            ],
            [
                'student_type' => StudentConstants::CON_STUDENT,
                'fee' => 101
            ]
        ];


        foreach ($hostelFees as $fee) {
            HostelFees::create($fee);
        }
    }
}
