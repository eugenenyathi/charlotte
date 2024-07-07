<?php

namespace App\Http\Services;

use App\Models\RoommatePreference;
use Illuminate\Support\Facades\DB;
use App\Constants\FacultyConstants;

class RoommatePreferenceService
{
    public function roommatePreferenceExists($studentID)
    {
        return RoommatePreference::where('student_id', $studentID)->exists();
    }

    public function getRoommatePreferences($studentID)
    {
        return RoommatePreference::where('student_id', $studentID)->first();
    }

    public function createRoommatePreference($request)
    {
        RoommatePreference::create([
            'student_id' => $request->student_id,
            'question_1' => $request->question_1,
            'question_2' => $request->question_2
        ]);
    }

    public function updateRoommatePreference($request)
    {
        RoommatePreference::where('student_id', $request->student_id)->update([
            'question_1' => $request->question_1,
            'question_2' => $request->question_2,
        ]);
    }
}
