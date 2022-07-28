<?php

namespace App\Models\SyncMasterData;

use Illuminate\Database\Eloquent\Model;

class SyncEnrollment extends Model
{
    protected $table = 'sync_enrollment';
    protected $primaryKey = 'enrollment_id';
    public $timestamps = false;

    protected $fillable =   [
                                'enrollment_id',
                                'course_id',
                                'enrollment_type',
                                'class',
                                'user_id',
                                'user_username',
                                'user_email',
                                'enrollment_status',
                                'sync_status',
                                'sync_date',
                                'flag_status',
                                'table_owner',
                                'table_id'
                            ];

    public static function ViewEnrollment($perpage=10, $faculty=null, $studyprogram=null, $course=null, $role=null, $keyword=null)
    {
        $query = SyncEnrollment::select(
                                            'enrollment_id',
                                            'sync_enrollment.sync_date as created_date',
                                            'B.category_name as faculty',
                                            'A.category_name as studyprogram',
                                            'sync_course.subject_code',
                                            'sync_course.subject_name as course_name',
                                            'sync_lms_course.class',
                                            'sync_user.name',
                                            'sync_user.jenis as role'
                                        );

        $query->leftjoin('sync_lms_course', 'sync_enrollment.course_id', '=', 'sync_lms_course.course_id');
        $query->leftjoin('sync_course', 'sync_lms_course.subject_id', '=', 'sync_course.subject_id');
        $query->leftjoin('sync_category as A', 'sync_course.category_id', '=', 'A.category_id');
        $query->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id');
        $query->leftJoin('sync_user', 'sync_enrollment.user_id', '=', 'sync_user.userid');

        // Check Faculty Filter
        if (!is_null($faculty) && $faculty != 0) 
        {
            $query->where('B.category_id', $faculty);
        }

        //Check Study Program Filter
        if (!is_null($studyprogram) && $studyprogram != 0) 
        {
            $query->where('A.category_id', $studyprogram);
        }

        //Check Study Program Filter
        if (!is_null($course) && $course != 0) 
        {
            $query->where('sync_lms_course.course_id', $course);
        }

        //Check Study Program Filter
        if (!is_null($role) && $role != 0) 
        {
            $query->where('sync_user.jenis', $role);
        }

        //Check Keywords Filter
        if (!is_null($keyword)) 
        {   
            $query->where('sync_user.name', 'ilike', '%'.$keyword.'%');
        }

        $data = $query->orderBy('enrollment_id')->paginate($perpage);

        return $data;
    }
}
