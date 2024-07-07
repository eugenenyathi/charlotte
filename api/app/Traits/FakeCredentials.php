<?php

namespace App\Traits;

use App\Models\Student;
use Illuminate\Support\Str;


trait FakeCredentials
{
    use Utils;

    private $letters = "ABCDEFGHIJKLMNOQRSTUVWXYZ";
    private $numbers = "0123456789";

    private function createFakeName($names)
    {
        /** 
         * 1. Get two random fullNames
         * 2. Explode each random fullNames
         * 3. One will be used for the firstName and the other for the lastname
         * 4. Create a new name
         */

        $randomNumber1 = $this->random(0, count($names) - 1);
        $randomName1 = $names[$randomNumber1];

        $randomNumber2 = $this->random($randomNumber1, count($names) - 1);
        $randomName2 = $names[$randomNumber2];

        $firstName = explode(" ", $randomName1)[0];
        $lastName = explode(" ", $randomName2)[1];

        return $firstName . " " . $lastName;
    }

    public function fakeStudentID()
    {
        while (true) {
            $studentID = $this->createStudentID();
            $idExists = Student::where('student_id', $studentID)->exists();

            if ($idExists) continue;
            return $studentID;
        }
    }

    public function fakeNationalID()
    {
        while (true) {
            $nationalID = $this->createNationalID();
            $idExists = Student::where('national_id', $nationalID)->exists();

            if ($idExists) continue;
            return $nationalID;
        }
    }

    private function createNationalID()
    {
        /*
            1. Generate 9 random numbers 
            2. shuffle the 9 random numbers
            2. Append the random with a province code
            3. Swap the number on index 9 with a random letter
        */

        // Generate the random numbers
        $randomNumbers = str_shuffle($this->numbers);
        // Get 9 numbers
        $nineNumbers = Str::substr($randomNumbers, 0, 9);
        $nineNumbers = str_shuffle($nineNumbers);
        // Append with a province code
        $partialID = $this->addProvinceCode($nineNumbers);
        //Do step 3
        $nationalID = $this->replaceWithLetter($partialID);

        return $nationalID;
    }

    private function createStudentID()
    {
        /*
            1. Generate 6 random numbers 
            2. shuffle the six numbers again to eliminate any possible duplicates
            3. shuffle the letters
            4. Append the L0 & add a random letter
            5. Verify if the students number already exists otherwise return
        */

        $randomNumbers = str_shuffle($this->numbers);
        $sixNumbers = Str::substr($randomNumbers, 0, 6);
        $sixNumbers = str_shuffle($sixNumbers);

        while (true) {
            $firstNumber = $sixNumbers[0];
            if ($firstNumber !== '0') break;
            else {
                $sixNumbers = str_shuffle($sixNumbers);
            }
        }

        $letters = str_shuffle($this->letters);
        $studentID = 'L0' . $sixNumbers . $letters[$this->random(0, Str::length($this->letters) - 1)];

        return $studentID;
    }

    private function fakeDob()
    {
        /*
            1. Generate day, month 
            2. Pick a year
        */
        $years = [1999, 2000, 2001, 2002, 2003];

        $day = $this->random(1, 31);
        $month = $this->random(1, 12);
        $year = $years[$this->random(0, 4)];

        if ($month === 2) {
            if ($year % 4 === 0) {
                $day = $this->random(1, 27);
            } else {
                $day = $this->random(1, 28);
            }
        } else if ($month === 4 || $month === 6 || $month === 9 || $month === 11) {
            $day = $this->random(1, 30);
        } else {
            $day = $this->random(1, 31);
        }


        if ($day < 10) $day = '0' . $day;
        if ($month < 10) $month = '0' . $month;

        $dob =  $year . '-' . $month . '-' .  $day;

        return $dob;
    }

    private function addProvinceCode($numbers)
    {
        $provinceCodes = ['79', '08', '06', '70'];
        $code = $provinceCodes[$this->random(0, 3)];

        return $code . '-' . $numbers;
    }

    //applies to generating a national ld
    private function replaceWithLetter($string)
    {
        $letters = str_shuffle($this->letters);
        $string[9] = $letters[$this->random(0, Str::length($this->letters) - 1)];

        return $string;
    }
}
