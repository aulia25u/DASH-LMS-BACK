<?php

namespace App\Models\SyncMasterData;

use Illuminate\Database\Eloquent\Model;

class SyncLmsCourse extends Model
{
    protected $table = 'sync_lms_course';
    protected $primaryKey = 'course_id';
    public $timestamps = false;

    protected $fillable =   [
                                'course_id',
                                'category_id',
                                'semester',
                                'class',
                                'subject_code',
                                'subject_name',
                                'sync_status',
                                'sync_date',
                                'flag_status',
                                'table_owner',
                                'table_id',
                                'subject_id',
                                'user_id',
                                'user_username',
                                'employeeid',
                                'lecturercode',
                                'employee_name',
                                'last_sync',
                                'is_synced',
                                'is_deleted',
                                'backup_state',
                                'backup_path',
                                'last_backup',
                                'backup_filename',
                                'delete_state',
                                'last_delete',
                                'course_completion_updated',
                                'last_completion_attempt',
                                'course_start',
                                'course_end',
                                'course_status'
                            ];

    public static function ViewCourseTeaching($faculty=null, $studyprogram=null)
    {
        $query = SyncLmsCourse::select(
                                            'course_id',
                                            'B.category_name as faculty',
                                            'A.category_name as studyprogram',
                                            'sync_course.subject_code',
                                            'sync_course.subject_name',
                                            'sync_course.subject_type',
                                            'sync_course.credit',
                                            'sync_course.curriculum_year',
                                            'sync_course.last_backup',
                                            'sync_course.is_backup',
                                            'sync_lms_course.course_status as active_status',
                                            'sync_lms_course.course_start',
                                            'sync_lms_course.course_end',
                                            'sync_lms_course.class',
                                            'sync_course.subject_id',
                                            'sync_lms_course.sync_status'
                                        );

        $query->leftjoin('sync_course', 'sync_lms_course.subject_id', '=', 'sync_course.subject_id');
        $query->leftjoin('sync_category as A', 'sync_course.category_id', '=', 'A.category_id');
        $query->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id');

        //Check Faculty Filter
        if (!is_null($faculty)) 
        {
            $query->where('B.category_id', $faculty);
        }

        //Check Study Program Filter
        if (!is_null($studyprogram)) 
        {
            $query->where('A.category_id', $studyprogram);
        }

        $data = $query->orderBy('course_id')->get();

        return $data;
    }

    public static function ViewCouseDetail()
    {
        $data = SyncLmsCourse::select(
                                        'sync_category.category_id',
                                        'sync_lms_course.course_id',
                                        'sync_course.subject_id',
                                        'sync_course.subject_code',
                                        'sync_course.subject_name',
                                        'sync_lms_course.class',
                                        'sync_lms_course.lecturercode'
                                     )
                ->join('sync_course', 'sync_lms_course.subject_id', '=', 'sync_course.subject_id')
                ->join('sync_category', 'sync_course.category_id', '=', 'sync_category.category_id')
                ->whereNull('sync_lms_course.is_synced')
                // ->orWhere('sync_lms_course.is_synced', false)
                ->where('sync_lms_course.course_id', 3123)
                // ->limit(1)
                ->get();

        return $data;
    }

    public static function ViewCouseDetailBackup()
    {
        $data = SyncLmsCourse::select(
                                        'sync_category.category_id',
                                        'sync_lms_course.course_id',
                                        'sync_course.subject_id',
                                        'sync_course.subject_code',
                                        'sync_course.subject_name',
                                        'sync_lms_course.class',
                                        'sync_lms_course.lecturercode'
                                     )
                ->join('sync_course', 'sync_lms_course.subject_id', '=', 'sync_course.subject_id')
                ->join('sync_category', 'sync_course.category_id', '=', 'sync_category.category_id')
                ->whereNotNull('sync_lms_course.is_synced')
                // ->orWhere('sync_lms_course.is_synced', true)
                ->where('sync_lms_course.course_id', 3123)
                // ->limit(1)
                ->get();

        return $data;
    }

    public static function ViewCouseDetailLMS()
    {
        $data = SyncLmsCourse::select(
                                        'sync_category.category_id',
                                        'sync_lms_course.course_id',
                                        'sync_course.subject_id',
                                        'sync_course.subject_code',
                                        'sync_course.subject_name',
                                        'sync_lms_course.class',
                                        'sync_lms_course.lecturercode',
                                        'sync_lms_course.backup_state',
                                        'sync_lms_course.backup_path'
                                     )
                ->join('sync_course', 'sync_lms_course.subject_id', '=', 'sync_course.subject_id')
                ->join('sync_category', 'sync_course.category_id', '=', 'sync_category.category_id')
                ->where('sync_lms_course.is_synced', true)
                ->where('sync_lms_course.course_id', 3123)
                // ->limit(1)
                ->get();

        return $data;
    }
}