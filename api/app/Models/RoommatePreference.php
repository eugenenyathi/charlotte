<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoommatePreference extends Model
{
    use HasFactory;

    public $table = "roommate_preferences";
    protected $fillable = ['student_id', 'question_1', 'question_2'];
    public $timestamps = false;
}
