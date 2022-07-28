<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'exam_questions';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'question_id',
                                'question_title',
                                'question_content',
                                'question_status',
                                'question_bank_id',
                                'question_bank_name',
                                'course_id'
                            ];
}
