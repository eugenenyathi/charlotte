<?php

namespace App\Constants;

class RoommatePreferenceConstants
{
    const QUESTION_1 = "Would you go out on a Friday night?";
    const QUESTION_2 = "Would you describe yourself as an introvert?";

    const YES =  'yes';
    const NO = 'no';

    const ANSWERS = [self::YES, self::NO];
    const QUESTIONS = [self::QUESTION_1, self::QUESTION_2];
}
