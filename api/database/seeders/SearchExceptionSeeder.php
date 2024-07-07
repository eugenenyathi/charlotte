<?php

namespace Database\Seeders;

use App\Constants\SearchConstants;
use App\Models\SearchException;
use Illuminate\Database\Seeder;
use App\Models\ActiveStudentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SearchExceptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        foreach (SearchConstants::EXCEPTIONS as $programId) {
            SearchException::create(['program_id' => $programId]);
        }
    }
}
