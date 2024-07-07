<?php

namespace App\Http\Services;

use App\Models\User;
use App\Traits\Utils;
use App\Traits\VUtils;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Residence;
use App\Models\RoomRange;
use App\Models\OldResidence;
use App\Models\LoginTimestamps;
use App\Constants\HostelConstants;
use Illuminate\Support\Facades\DB;

class HomeService
{
    use Utils;
    use VUtils;

    public function getRegistrationStatus($studentID)
    {
        return Payment::select('registered')->where('student_id', $studentID)->first();
    }

    public function getDashboardReminder($studentID)
    {

        return DB::table('students')
            ->join('payments', 'students.student_id', '=', 'payments.student_id')
            ->select('students.student_id', 'students.fullName', 'payments.amount_cleared', 'payments.registered')
            ->where('students.student_id', $studentID)
            ->first();
    }

    public function getTimestamps($studentID)
    {
        return LoginTimestamps::select('previous_stamp')->where('student_id', $studentID)->first();
    }

    public function getStudentProfile($studentID)
    {
        return DB::table('students')
            ->join('profile', 'students.student_id', '=', 'profile.student_id')
            ->join("programs", "profile.program_id", "programs.program_id")
            ->join("faculties", "programs.faculty_id", "faculties.faculty_id")
            ->select(
                'students.student_id',
                'students.fullName',
                'students.gender',
                'profile.student_type',
                'faculties.faculty',
                'programs.program',
                'profile.part',
                'profile.enrolled'
            )
            ->where('students.student_id', $studentID)
            ->first();
    }

    public function getStudentResidence($studentID)
    {
        return Residence::select('hostel', 'room', 'part')
            ->where('student_id', $studentID)
            ->first();
    }

    public function getStudentOldResidence($studentID)
    {
        return OldResidence::select('hostel', 'room', 'part')
            ->where('student_id', $studentID)
            ->orderBy('part', 'asc')
            ->get();
    }

    public function roomSpecifics($hostel, $room)
    {
        switch ($hostel) {
            case HostelConstants::GIRLS_HOSTEL:
                return RoomRange::select('girls_wing', 'floor', 'girls_floor_side')
                    ->where('last_room', '>=', $room)->first();


            case HostelConstants::BOYS_HOSTEL:
                return RoomRange::select('boys_wing', 'floor', 'boys_floor_side')
                    ->where('last_room', '>=', $room)->first();
        }
    }

    public function getUserPassword($studentID)
    {
        return User::select('password')->where('student_id', $studentID)->first();
    }

    public function updateUserPassword($studentID, $password)
    {
        User::where('student_id', $studentID)->update(['password' => $password]);
    }
}
