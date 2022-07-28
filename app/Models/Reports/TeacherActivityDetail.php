<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class TeacherActivityDetail extends Model
{
    protected $table = 'reporting_teacher_activities_detail';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable =   [
                                'course_id',
                                'username',
                                'idnumber',
                                'login',
                                'grading',
                                'discussion',
                                'created_at',
                                'updated_at',
                                'sync_status',
                            ];
}
