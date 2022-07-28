<?php

namespace App\Models\Schedule;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Schedule extends Model
{
    protected $table = 'schedule_exams';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'topicname',
                                'examdate',
                                'slotid',
                                'totalexamparticipant',
                                'coursemaster',
                                'courseparallel',
                                'coordinator',
                                'is_parallel',
                                'is_verified',
                                'is_deployed',
                                'status'
                            ];

    //Verify List
    public static function GetExamVerifyList($perpage=10, $faculty=null, $studyprogram=null, $startdate=null, $enddate=null, $starttime=null, $endtime=null, $status=null, $keywords=null)
    {
        $data = Schedule::select(
                                    'schedule_exams.id',
                                    'schedule_exams.topicname',
                                    'schedule_exams.examdate',
                                    'schedule_exams.totalexamparticipant',
                                    'schedule_exams.slotid',
                                    'schedule_exams.coursemaster as courseid',
                                    'sync_lms_course.subject_code',
                                    'sync_lms_course.subject_name',
                                    'sync_lms_course.class',
                                    'schedule_exams.courseparallel',
                                    'schedule_exams.status'
                                )
                ->join('sync_lms_course', 'sync_lms_course.course_id', '=', 'schedule_exams.coursemaster')
                ->where('schedule_exams.is_parallel', 1)
                ->where('schedule_exams.is_verified', 0)
                ->where('schedule_exams.is_deployed', 0)
                ->orderBy('schedule_exams.id')
                ->paginate($perpage);

        return $data;
    }

    //Deploy List
    public static function GetExamDeployList($perpage=10, $faculty=null, $studyprogram=null, $startdate=null, $enddate=null, $starttime=null, $endtime=null, $status=null, $keywords=null)
    {
        $data = Schedule::select(
                                    'schedule_exams.id',
                                    'schedule_exams.topicname',
                                    'schedule_exams.examdate',
                                    'schedule_exams.totalexamparticipant',
                                    'schedule_exams.slotid',
                                    'schedule_exams.coursemaster as courseid',
                                    'sync_lms_course.subject_code',
                                    'sync_lms_course.subject_name',
                                    'sync_lms_course.class',
                                    'schedule_exams.courseparallel',
                                    'schedule_exams.status'
                                )
                ->join('sync_lms_course', 'sync_lms_course.course_id', '=', 'schedule_exams.coursemaster')
                ->where('schedule_exams.is_verified', 1)
                ->orderBy('schedule_exams.id')
                ->paginate($perpage);

        return $data;
    }
}