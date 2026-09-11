<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentRecord extends Eloquent
{
    use HasFactory;

    protected $fillable = [
        'session', 'user_id', 'my_class_id', 'my_parent_id', 'adm_no', 'year_admitted', 'grad', 'grad_date', 'house', 'age', 'status', 'admission_date', 'previous_school'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function my_parent()
    {
        return $this->belongsTo(User::class);
    }

    public function my_class()
    {
        return $this->belongsTo(MyClass::class);
    }

    public function guardians()
    {
        return $this->hasMany(StudentGuardian::class, 'student_id', 'user_id');
    }

    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id', 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_id', 'user_id');
    }

    public function discipline()
    {
        return $this->hasMany(StudentDiscipline::class, 'student_id', 'user_id');
    }

    public function health()
    {
        return $this->hasOne(StudentHealth::class, 'student_id', 'user_id');
    }

    public function transport()
    {
        return $this->hasOne(StudentTransport::class, 'student_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(StudentActivity::class, 'student_id', 'user_id');
    }

    public function events()
    {
        return $this->hasMany(StudentEvent::class, 'student_id', 'user_id');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'student_id', 'user_id');
    }
}
