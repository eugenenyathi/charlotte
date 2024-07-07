<?php

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
        Schema::create('active_student_type', function (Blueprint $table) {
            $table->id();
            $table->enum('student_type', StudentConstants::STUDENT_TYPE);
            $table->enum('active', StudentConstants::ACTIVE_STATUS);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('active_student_type');
    }
};
