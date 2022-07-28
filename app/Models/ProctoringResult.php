<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProctoringResult extends Model
{
    protected $table = 'exam_proctoring_result';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'quiz_id',
                                'firstname',
                                'lastname',
                                'timestamp',
                                'log_id',
                                'course_id',
                                'id_quiz',
                                'user_id',
                                'webcampicture',
                                'status',
                                'awsscore',
                                'awsflag',
                                'timemodified',
                                'email'
                            ];
}
