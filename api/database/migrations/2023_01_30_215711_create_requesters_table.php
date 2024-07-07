<?php

use App\Constants\RequestProcessed;
use App\Constants\StudentConstants;
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
        Schema::create('requesters', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->enum('student_type', StudentConstants::STUDENT_TYPE);
            $table->enum('gender', StudentConstants::GENDER);
            $table->enum('processed', RequestProcessed::STATUS)->default(RequestProcessed::NO);
            $table->timestamps();

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
        Schema::dropIfExists('requesters');
    }
};
