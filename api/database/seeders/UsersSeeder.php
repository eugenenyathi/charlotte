<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\LoginTimestamps;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $students = $this->fetchStudents();

        foreach ($students as $student) {
            User::create([
                'student_id' => $student->student_id,
                'password' => Hash::make('12345678')
            ]);

            // update the login timestamp
            LoginTimestamps::create([
                'student_id' => $student->student_id,
                'current_stamp' => now(),
            ]);
        }
    }

    //students without accounts
    private function fetchStudents()
    {
        $students = Student::select('student_id')->get();
        $hasNoAccount = [];

        foreach ($students as $student) {
            $hasAccount = User::where('student_id', $student->student_id)->exists();

            if ($hasAccount) continue;
            else $hasNoAccount[] = $student;
        }

        return $hasNoAccount;
    }
}
