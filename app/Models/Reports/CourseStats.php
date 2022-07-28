<?php

namespace App\Models\Reports;

use Illuminate\Database\Eloquent\Model;

class CourseStats extends Model
{
    protected $table = 'reporting_course_stats';
    protected $primaryKey = 'course_id';
    protected $fillable =   [
                                'course_id',
                                'subject_code',
                                'subject_name',
                                'percent_profile',
                                'percent_topic',
                                'list_sections',
                                'section',
                                'file',
                                'assignment',
                                'quiz',
                                'url',
                                'forum',
                                'sync_status'
                            ];
}
