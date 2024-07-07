<?php

use App\Constants\HostelConstants;
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
        Schema::create('room_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('first_room')->unique();
            $table->integer('last_room')->unique();

            $table->enum('floor', HostelConstants::HOSTEL_FLOORS);

            $table->enum('girls_wing', HostelConstants::HOSTEL_DIRECTIONS);
            $table->enum('boys_wing', HostelConstants::HOSTEL_DIRECTIONS);

            $table->enum('girls_floor_side', HostelConstants::HOSTEL_DIRECTIONS);
            $table->enum('boys_floor_side', HostelConstants::HOSTEL_DIRECTIONS);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_ranges');
    }
};
