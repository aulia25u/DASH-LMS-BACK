<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class TeacherEditing extends Model
{
    protected $table = 'reporting_teacher_editing';
    protected $primaryKey = 'course_id';
    protected $fillable =   [
                                'course_id',
                                'start_date',
                                'number_teachers',
                                'created_at',
                                'updated_at',
                                'sync_status',
                            ];
}
