<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomRange extends Model
{
    use HasFactory;

    public $table = 'room_ranges';
    public $timestamps = false;

    protected $fillable = ['first_room', 'last_room', 'floor', 'girls_wing', 'boys_wing', 'girls_floor_side', 'boys_floor_side'];
}
