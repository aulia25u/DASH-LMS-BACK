<?php

namespace App\Models\SyncMasterData;

use Illuminate\Database\Eloquent\Model;

class SyncCourse extends Model
{
    protected $table = 'sync_course';
    protected $primaryKey = 'subject_id';
    public $timestamps = false;

    protected $fillable =   [
                                'subject_id',
                                'subject_code',
                                'subject_name',
                                'subject_type',
                                'subject_ppdu',
                                'credit',
                                'curriculum_year',
                                'sync_by',
                                'sync_status',
                                'sync_date',
                                'flag_status',
                                'table_owner',
                                'table_id',
                                'category_id',
                                'studyprogramid',
                                'approve_status',
                                'approve_date',
                                'notes',
                                'approve_by',
                                'input_by',
                                'input_date',
                                'last_backup',
                                'is_backup',
                                'is_manual_insert',
                                'is_deleted',
                                'subject_desc',
                                'subject_status'
                            ];

    public static function ViewCourseDevelopment($faculty=null, $studyprogram=null)
    {
        $query = SyncCourse::select(
                                        'subject_id',
                                        'sync_course.table_id',
                                        'subject_id as course_id',
                                        'B.category_name as faculty',
                                        'A.category_name as studyprogram',
                                        'subject_code',
                                        'subject_name',
                                        'subject_type',
                                        'credit',
                                        'curriculum_year',
                                        'last_backup',
                                        'is_backup',
                                        'subject_status as active_status',
                                        'sync_course.sync_status'
                                    );

        $query->join('sync_category as A', 'sync_course.category_id', '=', 'A.category_id');
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

        $data = $query->orderBy('sync_course.subject_id')->get();

        return $data;
    }

    public static function ViewCourseDevelopmentSync($faculty=null, $studyprogram=null)
    {
        if (is_null($faculty)) 
        {
            $data = SyncCourse::select(
                                        'subject_id',
                                        'B.category_name as faculty',
                                        'A.category_name as studyprogram',
                                        'subject_code',
                                        'subject_name',
                                        'subject_type',
                                        'credit',
                                        'curriculum_year',
                                        'last_backup',
                                        'is_backup',
                                        'subject_status as active_status'
                                      )
                    ->join('sync_category as A', 'sync_course.category_id', '=', 'A.category_id')
                    ->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id')
                    ->whereNotNull('is_backup')
                    ->get();
        }
        else
        {
            $data = SyncCourse::select(
                                        'subject_id',
                                        'B.category_name as faculty',
                                        'A.category_name as studyprogram',
                                        'subject_code',
                                        'subject_name',
                                        'subject_type',
                                        'credit',
                                        'curriculum_year',
                                        'last_backup',
                                        'is_backup',
                                        'subject_status as active_status'
                                      )
                    ->join('sync_category as A', 'sync_course.category_id', '=', 'A.category_id')
                    ->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id')
                    ->where('A.category_id', $studyprogram)
                    ->whereNotNull('is_backup')
                    ->get();
        }

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

    public static function FilterCourse($parent=null)
    {
        $query = SyncCourse::select('subject_id', 'subject_code', 'subject_name');

        if (!is_null($parent)) 
        {
            $query->where('category_id', $parent);
        }

        $data = $query->orderBy('subject_id')->get();

        return $data;
    }
}
