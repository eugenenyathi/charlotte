<?php

namespace Database\Seeders;

use App\Constants\RoommatePreferenceConstants;
use App\Models\RoommatePreference;
use App\Models\Student;
use App\Traits\Utils;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoommatePreferenceSeeder extends Seeder
{
    use Utils;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = Student::select('student_id')->get();

        foreach ($students as $student) {
            $studentPreferencesExists = RoommatePreference::where('student_id', $student->student_id)->exists();

            if (!$studentPreferencesExists) {
                $preferences = $this->randomizedPreferences();

                RoommatePreference::create([
                    'student_id' => $student->student_id,
                    'question_1' => $preferences[0],
                    'question_2' => $preferences[1],
                ]);
            }
        }
    }

    public function randomizedPreferences()
    {
        $preferences = [];

        while (true) {
            if (count($preferences) === 2) break;
            $preferences[] = RoommatePreferenceConstants::ANSWERS[$this->random(0, 1)];
        }

        return $preferences;
    }
}
