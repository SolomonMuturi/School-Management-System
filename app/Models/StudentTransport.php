<?php

namespace App\Models;

use App\User;
use Eloquent;

class StudentTransport extends Eloquent
{
    protected $table = 'student_transport';

    protected $fillable = ['student_id', 'route_name', 'pickup_point', 'vehicle_no', 'driver_name', 'driver_phone', 'status', 'notes'];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}