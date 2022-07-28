<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

use App\Models\Schedule\Slot;
use App\Models\Schedule\Schedule;
use App\Models\SyncMasterData\SyncLmsCourse;

class ScheduleMonitorController extends Controller
{
    //View Verify
    public function ViewTopicActivityVerification($perpage=10, $faculty=null, $studyprogram=null, $startdate=null, $enddate=null, $starttime=null, $endtime=null, $status=null, $keywords=null)
    {
        $verifylist = Schedule::GetExamVerifyList($perpage, $faculty, $studyprogram, $startdate, $enddate, $starttime, $endtime, $status, $keywords);

        //decode course parallel & slotid
        foreach ($verifylist as $key => $value) 
        {
            $verifylist[$key]['courseparallel'] = json_decode($verifylist[$key]['courseparallel']);
            $verifylist[$key]['slotid'] = json_decode($verifylist[$key]['slotid']);

            $min = min(array_keys($verifylist[$key]['slotid']));
            $max = max(array_keys($verifylist[$key]['slotid']));

            $slotmin = Slot::select('starttime')->where('id', $verifylist[$key]['slotid'][$min])->first();
            $slotmax = Slot::select('endtime')->where('id', $verifylist[$key]['slotid'][$max])->first();

            $verifylist[$key]['start_time'] = strtotime($verifylist[$key]['examdate'].' '.$slotmin->starttime);
            $verifylist[$key]['end_time'] = strtotime($verifylist[$key]['examdate'].' '.$slotmax->endtime);

            if ($verifylist[$key]['courseparallel'] != '') 
            {
                $parallel = SyncLmsCourse::select('course_id', 'subject_code', 'subject_name', 'class')
                            ->whereIn('course_id', $verifylist[$key]['courseparallel'])
                            ->get();

                $verifylist[$key]['courseparallel'] = $parallel;
            }

            if ($verifylist[$key]['slotid'] != '') 
            {
                $slots = Slot::select('id', 'slotname', 'starttime', 'endtime')
                         ->whereIn('id', $verifylist[$key]['slotid'])
                         ->get();

                $verifylist[$key]['slotid'] = $slots;
            }
        }

        return $verifylist;
    }

    //View Deploy
    public function ViewTopicDeploy($perpage=10, $faculty=null, $studyprogram=null, $startdate=null, $enddate=null, $starttime=null, $endtime=null, $status=null, $keywords=null)
    {
        $verifylist = Schedule::GetExamDeployList($perpage, $faculty, $studyprogram, $startdate, $enddate, $starttime, $endtime, $status, $keywords);

        //decode course parallel & slotid
        foreach ($verifylist as $key => $value) 
        {
            $verifylist[$key]['courseparallel'] = json_decode($verifylist[$key]['courseparallel']);
            $verifylist[$key]['slotid'] = json_decode($verifylist[$key]['slotid']);

            $min = min(array_keys($verifylist[$key]['slotid']));
            $max = max(array_keys($verifylist[$key]['slotid']));

            $slotmin = Slot::select('starttime')->where('id', $verifylist[$key]['slotid'][$min])->first();
            $slotmax = Slot::select('endtime')->where('id', $verifylist[$key]['slotid'][$max])->first();

            $verifylist[$key]['start_time'] = strtotime($verifylist[$key]['examdate'].' '.$slotmin->starttime);
            $verifylist[$key]['end_time'] = strtotime($verifylist[$key]['examdate'].' '.$slotmax->endtime);

            if ($verifylist[$key]['courseparallel'] != '') 
            {
                $parallel = SyncLmsCourse::select('course_id', 'subject_code', 'subject_name', 'class')
                            ->whereIn('course_id', $verifylist[$key]['courseparallel'])
                            ->get();

                $verifylist[$key]['courseparallel'] = $parallel;
            }

            if ($verifylist[$key]['slotid'] != '') 
            {
                $slots = Slot::select('id', 'slotname', 'starttime', 'endtime')
                         ->whereIn('id', $verifylist[$key]['slotid'])
                         ->get();

                $verifylist[$key]['slotid'] = $slots;
            }
        }

        return $verifylist;
    }
}
