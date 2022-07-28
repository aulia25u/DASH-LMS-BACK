<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'exam_quiz';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'course_id',
                                'quiz_id',
                                'quiz_name',
                                'timeopen',
                                'timeclose',
                                'timelimit',
                                'attempts',
                                'number_questions',
                            ];
}