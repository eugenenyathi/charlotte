<?php

use App\Constants\HostelConstants;
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
        Schema::create('residence', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->enum('student_type', StudentConstants::STUDENT_TYPE);
            $table->enum('hostel', HostelConstants::HOSTELS);
            $table->integer('room');
            $table->enum('part', StudentConstants::LEVELS);
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
        Schema::dropIfExists('residence');
    }
};
