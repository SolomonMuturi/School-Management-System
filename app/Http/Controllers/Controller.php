<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Route params for student/mark/pin flows carry a hashed (non-numeric) id.
     * Convert back to the raw id when the value is not already numeric.
     */
    protected function decodeStudentId($student_id)
    {
        return is_numeric($student_id) ? $student_id : \App\Helpers\Qs::decodeHash($student_id);
    }
}
