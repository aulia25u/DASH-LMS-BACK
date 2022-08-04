<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'exam_groups';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'course_id',
                                'group_id',
                                'group_name',
                                'group_desc',
                                'group_section'
                            ];
}
