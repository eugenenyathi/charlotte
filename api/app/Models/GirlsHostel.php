<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GirlsHostel extends Model
{
    use HasFactory;

    public $table = 'girls_hostel';
    public $timestamps = false;

    protected $fillable = ['room', 'usable', 'con_occupied', 'block_occupied'];
}
