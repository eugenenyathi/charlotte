<?php

namespace App\Constants;

class ProgramConstants
{
    /** If you update these, also update the programs array down the valley */
    const PROGRAM_IDS = [
        'ASAS', 'ASCS', 'ASBT', 'ASFS', 'ASEC', 'ASEB', 'ASIR',
        'EAPE', 'EAIT', 'EABC',
        'COAF', 'COBK', 'COHR', 'COTH',
        'HSGS', 'HSUP', 'HSWN', 'HSMS', 'HSDS',
        'HSSC', 'HSLC',
    ];

    const AGRIC_ENG_IDS = [
        'ASAS', 'ASCS', 'ASBT', 'ASFS', 'ASEC', 'ASEB', 'ASIR',
        'EAPE', 'EAIT', 'EABC',
    ];

    const HS_IDS = [
        'HSGS', 'HSUP', 'HSWN', 'HSMS', 'HSDS',
        'HSSC', 'HSLC',
    ];

    const COMMERCE_IDS = [
        'COAF', 'COBK', 'COHR', 'COTH',
    ];

    /** ============= AGRICULTURE ========================== */
    const ANIMAL_SCIENCE = [
        'program_id' => 'ASAS',
        'program' => 'Animal Science',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];
    const CROP_SCIENCE = [
        'program_id' => 'ASCS',
        'program' => 'Crop Science',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];

    const FOOD_SCIENCE = [
        'program_id' => 'ASFS',
        'program' => 'Food Science',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];

    const BIO_TECH = [
        'program_id' => 'ASBT',
        'program' => 'Bio Technology',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];

    const ENVIRONMENT =  [
        'program_id' => 'ASEC',
        'program' => 'Environment And Conservation Studies',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];

    const IRRIGATION =  [
        'program_id' => 'ASIR',
        'program' => 'Irrigation And Water Conservation Studies',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];

    const AGRIC_ECONOMICS =  [
        'program_id' => 'ASEB',
        'program' => 'Agriculture Economics',
        'faculty_id' => FacultyConstants::AGRIC_FACULTY['faculty_id']
    ];


    /** ============= ENGINEERING ========================== */

    const IT = [
        'program_id' => 'EAIT',
        'program' => 'Information Technology',
        'faculty_id' => FacultyConstants::ENGINEERING_FACULTY['faculty_id']
    ];

    const BUSINESS_COMPUTING = [
        'program_id' => 'EABC',
        'program' => 'Business Computing',
        'faculty_id' => FacultyConstants::ENGINEERING_FACULTY['faculty_id']
    ];

    const PRODUCTION_ENG = [
        'program_id' => 'EAPE',
        'program' => 'Production Engineering',
        'faculty_id' => FacultyConstants::ENGINEERING_FACULTY['faculty_id']
    ];

    /** ============= COMMERCE ========================== */

    const ACCOUNTING =  [
        'program_id' => 'COAF',
        'program' => 'Accounting',
        'faculty_id' => FacultyConstants::COMMERCE_FACULTY['faculty_id']
    ];

    const BANKING =  [
        'program_id' => 'COBK',
        'program' => 'Banking And Finance',
        'faculty_id' => FacultyConstants::COMMERCE_FACULTY['faculty_id']
    ];

    const TOURISM =  [
        'program_id' => 'COTH',
        'program' => 'Tourism And Hospitality',
        'faculty_id' => FacultyConstants::COMMERCE_FACULTY['faculty_id']
    ];

    const HR =  [
        'program_id' => 'COHR',
        'program' => 'Human And Resource Management',
        'faculty_id' => FacultyConstants::COMMERCE_FACULTY['faculty_id']
    ];


    /** ============= HUMANITIES ========================== */

    const GIS =  [
        'program_id' => 'HSGS',
        'program' => 'Geography And Information Systems',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const URBAN_PLANNING =  [
        'program_id' => 'HSUP',
        'program' => 'Urban Planning And Development',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const WILDLIFE_NATURAL_RESOURCES =  [
        'program_id' => 'HSWN',
        'program' => 'Wildlife And Natural Resources',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const DIV =  [
        'program_id' => 'HSDS',
        'program' => 'Development Studies',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const MEDIA =  [
        'program_id' => 'HSMS',
        'program' => 'Media Studies',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const SOCIOLOGY = [
        'program_id' => 'HSSC',
        'program' => 'Sociology',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const LANGUAGES =  [
        'program_id' => 'HSLC',
        'program' => 'Language And Communication',
        'faculty_id' => FacultyConstants::HUMANITIES_FACULTY['faculty_id']
    ];

    const PROGRAMS = [
        self::ANIMAL_SCIENCE,
        self::CROP_SCIENCE,
        self::FOOD_SCIENCE,
        self::BIO_TECH,
        self::AGRIC_ECONOMICS,
        self::ENVIRONMENT,
        self::IRRIGATION,

        self::PRODUCTION_ENG,
        self::IT,
        self::BUSINESS_COMPUTING,

        self::ACCOUNTING,
        self::BANKING,
        self::HR,
        self::TOURISM,

        self::GIS,
        self::URBAN_PLANNING,
        self::WILDLIFE_NATURAL_RESOURCES,
        self::MEDIA,
        self::DIV,
        self::SOCIOLOGY,
        self::LANGUAGES,
    ];
}
