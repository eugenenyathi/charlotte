<?php

use App\Constants\RoommatePreferenceConstants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roommate_preferences', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->enum('question_1', RoommatePreferenceConstants::ANSWERS);
            $table->enum('question_2', RoommatePreferenceConstants::ANSWERS);
            // $table->enum('question_3', RoommatePreferenceConstants::ANSWERS);

            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roommate_preferences');
    }
};
