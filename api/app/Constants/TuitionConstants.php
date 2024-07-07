<?php

namespace App\Constants;

class TuitionConstants
{
    const AGRIC_TUITION = [
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id'],
        'con_amount' => 700,
        'block_amount' => 500
    ];

    const ENGINEERING_TUITION = [
        'faculty_id' => FacultyConstants::ENGINEERING_FACULTY['faculty_id'],
        'con_amount' => 700,
        'block_amount' => 500
    ];

    const COMMERCE_TUITION = [
        'faculty_id' => FacultyConstants::COMMERCE_FACULTY['faculty_id'],
        'con_amount' => 620,
        'block_amount' => 450
    ];

    const HUMANITIES_TUITION =
    [
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id'],
        'con_amount' => 650,
        'block_amount' => 470
    ];

    const TUITION = [self::AGRIC_TUITION, self::ENGINEERING_TUITION, self::COMMERCE_TUITION, self::HUMANITIES_TUITION];
}
