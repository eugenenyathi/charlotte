<?php

namespace App\Constants;

class FacultyConstants
{
    const FACULTY_IDS = ['AS', 'EA', 'HS', 'CO'];

    const AGRIC_FACULTY = [
        'faculty_id' => 'AS',
        'faculty_name' => 'Agricultural Sciences'
    ];

    const ENGINEERING_FACULTY = [
        'faculty_id' => 'EA',
        'faculty_name' => 'Engineering'
    ];

    const HUMANITIES_FACULTY = [
        'faculty_id' => 'HS',
        'faculty_name' => 'Humanities'
    ];

    const COMMERCE_FACULTY = [
        'faculty_id' => 'CO',
        'faculty_name' => 'Commerce'
    ];

    const FACULTIES = [self::AGRIC_FACULTY, self::ENGINEERING_FACULTY, self::COMMERCE_FACULTY, self::HUMANITIES_FACULTY];
}
