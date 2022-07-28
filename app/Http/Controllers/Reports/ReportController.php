<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

use App\Models\Reports\TeacherActivity;
use App\Models\Reports\TeacherEditing;
use App\Models\Reports\CourseStats;

class ReportController extends Controller
{
    public function ReportTeacherActivity(){ 
        $data = TeacherActivity::join('reporting_teacher_activities_detail as b', 'reporting_teacher_activities.course_id', 'b.course_id')
                ->where('reporting_teacher_activities.sync_status','SYNCED')
                ->where('b.sync_status','SYNCED')->get();

        return response()->json($data);
    }

    public function ReportTeacherEditing(){ 
        $data = TeacherEditing::join('reporting_teacher_editing_detail as b', 'reporting_teacher_editing.course_id', 'b.course_id')
                ->where('reporting_teacher_editing.sync_status','SYNCED')
                ->where('b.sync_status','SYNCED')->get();

        return response()->json($data);
    }

    public function ReportCourseStats(){ 
        $data = CourseStats::select()->where('sync_status','SYNCED')->get();

        return response()->json($data);
    }
}
