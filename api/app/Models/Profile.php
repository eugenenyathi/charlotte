<?php

namespace App\Models;

use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    public $table = 'profile';
    protected $fillable = ['student_id', 'program_id', 'part', 'student_type', 'enrolled'];
    public $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function program()
    {
        return $this->hasOne(Program::class);
    }
}
