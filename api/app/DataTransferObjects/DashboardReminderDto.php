<?php

namespace App\DataTransferObjects;

use App\Traits\Utils;
use Illuminate\Support\Str;

class DashboardReminderDto
{
    public $student;

    use Utils;

    public function __construct($student)
    {

        $this->student = $student;
    }

    public function data()
    {
        return [
            'name' => Str::before($this->student->fullName, ' '),
            'tuition' => $this->facultyTuition($this->student->student_id),
            'amount_cleared' => $this->student->amount_cleared,
            'registered' => $this->student->registered
        ];
    }
}
