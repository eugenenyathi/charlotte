<?php

use App\Constants\SelectionResponse;
use App\Constants\StudentConstants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('requester_id');
            $table->string('selected_roommate');
            $table->enum('student_type', StudentConstants::STUDENT_TYPE);
            $table->enum('gender', StudentConstants::GENDER);
            $table->enum('selection_confirmed', SelectionResponse::RESPONSES)->default(SelectionResponse::WAITING);

            $table->foreign('requester_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('selected_roommate')->references('student_id')->on('students')->onDelete('cascade');

            $table->index('requester_id');
            $table->index('selected_roommate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_candidates');
    }
};
