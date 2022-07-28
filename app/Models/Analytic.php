<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    protected $table = 'exam_analytics';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'course_id',
                                'quiz_id',
                                'question_id',
                                'question_content',
                                'question_answer',
                                'user_right_answer',
                                'user_wrong_answer',
                                'user_unanswered',
                            ];
}
