<?php

namespace App\Models;

use Eloquent;

class CurriculumTopic extends Eloquent
{
    protected $fillable = ['curriculum_id', 'subject_id', 'my_class_id', 'topic', 'learning_objectives', 'term'];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function myClass()
    {
        return $this->belongsTo(MyClass::class, 'my_class_id');
    }
}