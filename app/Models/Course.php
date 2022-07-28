<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'exam_courses';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'category_id',
                                'category_name',
                                'course_id',
                                'course_name',
                                'offset',
                                'proctoring_offset'
                            ];
}
