<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class TeacherEditingDetail extends Model
{
    protected $table = 'reporting_teacher_editing_detail';
    protected $primaryKey = 'course_id';
    protected $fillable =   [
                                'course_id',
                                'username',
                                'idnumber',
                                'section',
                                'label',
                                'question',
                                'attendance',
                                'quiz',
                                'assignment',
                                'forum',
                                'page',
                                'sync_status'
                            ];
}
