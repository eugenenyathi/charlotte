<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'national_id', 'dob', 'fullName', 'gender'];
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(User::class, 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
