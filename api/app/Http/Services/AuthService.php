<?php

namespace App\Http\Services;

use App\Models\User;
use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\Student;
use App\Models\LoginTimestamps;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    use Utils;
    use VUtils;

    public function verifyNationalID($studentID, $nationalID)
    {
        return Student::where('student_id', $studentID)->where('national_id', $nationalID)->first();
    }

    public function verifyDOB($studentID, $dob)
    {
        return Student::where('student_id', $studentID)->where('dob', $dob)->first();
    }

    public function accountExists($studentID)
    {
        return User::where('student_id', $studentID)->exists();
    }

    public function getUser($studentID)
    {
        return User::where('student_id', $studentID)->first();
    }

    public function createUser($studentID, $password)
    {
        return User::create([
            'student_id' => $studentID,
            'password' => Hash::make($password)
        ]);
    }

    public function updateUserPassword($studentID, $password)
    {
        return User::where('student_id', $studentID)->update([
            'password' => Hash::make($password)
        ]);
    }

    public function logTimestamp($studentID)
    {
        LoginTimestamps::create([
            'student_id' => $studentID,
            'current_stamp' => now(),
        ]);
    }

    public function getCurrentTimestamp($studentID)
    {
        return LoginTimestamps::select('current_stamp')->where('student_id', $studentID)->first();
    }

    public function updateTimestamps($previousTimestamp, $studentID)
    {
        //update the timestamps
        LoginTimestamps::where('student_id', $studentID)
            ->update([
                'previous_stamp' => $previousTimestamp,
                'current_stamp' => now()
            ]);
    }

    public function getStudentFullName($studentID)
    {
        return Student::select('fullName')->where('student_id', $studentID)->first()->fullName;
    }
}
