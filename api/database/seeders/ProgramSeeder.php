<?php

namespace Database\Seeders;

use App\Constants\ProgramConstants;
use Illuminate\Database\Seeder;
use App\Models\Program;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        foreach (ProgramConstants::PROGRAMS as $program) {
            if (Program::where('program_id', $program['program_id'])->exists()) continue;
            else {
                Program::create([
                    'program_id' => $program['program_id'],
                    'program' => $program['program'],
                    'faculty_id' => $program['faculty_id']
                ]);
            }
        }
    }
}
