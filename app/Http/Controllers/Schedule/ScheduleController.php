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
use App\Models\SyncMasterData\SyncEnrollment;
use App\Models\SyncMasterData\SyncUser;

class ScheduleController extends Controller
{
    //Create Schedule
    public function CreateSchedule(Request $request)
    {
        //Check Parallel or Not
        if (isset($request->courseparallel)) 
        {
            $courseparallel = explode(',', $request->courseparallel);
            $getparallelparticipant = SyncEnrollment::whereIn('course_id', $courseparallel)->where('enrollment_type', 'MAHASISWA')->count();
            $getmasterparticipant = SyncEnrollment::where('course_id', $request->coursemaster)->where('enrollment_type', 'MAHASISWA')->count();

            $totalpart = $getparallelparticipant + $getmasterparticipant;
        }
        else
        {
            $courseparallel = null;
            $getparallelparticipant = 0;
            $getmasterparticipant = SyncEnrollment::where('course_id', $request->coursemaster)->where('enrollment_type', 'MAHASISWA')->count();

            $totalpart = $getparallelparticipant + $getmasterparticipant;
        }

        //Check Slot
        if (isset($request->slotid)) 
        {
            $slotid = explode(',', $request->slotid);
        }

        //Create Schedule
        $save = Schedule::create([
                                    'topicname' => $request->topicname,
                                    'examdate' => $request->examdate,
                                    'slotid' => json_encode($slotid),
                                    'totalexamparticipant' => isset($request->totalexamparticipant) ? $request->totalexamparticipant : $totalpart,
                                    'coursemaster' => $request->coursemaster,
                                    'courseparallel' => isset($request->courseparallel) ? json_encode($courseparallel) : null,
                                    'coordinator' => $request->coordinator,
                                    'is_parallel' => isset($request->courseparallel) ? 1 : 0,
                                    'is_verified' => isset($request->courseparallel) ? 0 : 1,
                                    'is_deployed' => isset($request->courseparallel) ? 0 : 1,
                                    'status' => isset($request->courseparallel) ? 'Waiting Topic & Activity Verification' : 'Done',
                                ]);

        if ($save) 
        {
            //Check if parallel
            if (!isset($request->courseparallel)) 
            {
                $response = Http::asForm()->post(env('LMS_DN'), 
                            [
                                'wstoken' => env('LMS_TOKEN_SYNC'),
                                'wsfunction' => 'local_academic_api_course_section_check',
                                'moodlewsrestformat' => 'json',
                                'source' => $request->coursemaster, //source course id
                                'section' => $request->topicname, //source topic name from source course id
                                // 'target[0][id]' => $request->coursemaster, //target course id for section import 
                            ]);

                $decode = json_decode($response, true);

                //Return
                if (isset($decode['status'])) 
                {
                    if ($decode['status'] == true) 
                    {
                        return response()->json(['status' => 'Success', 'message' => 'Success create schedule exam'], 200);
                    }
                    else
                    {
                        return response()->json(['status' => 'Failed', 'message' => 'Failed create schedule exam', 'detail' => $decode['exception']], 500);
                    }
                }
                else
                {
                    return response()->json(['status' => 'Failed', 'message' => 'Failed create schedule exam'], 500);
                }
            }

            //If not parallel
            return response()->json(['status' => 'Success', 'message' => 'Success create schedule exam'], 200);
        }
        else
        {
            return response()->json(['status' => 'Failed', 'message' => 'Failed update schedule exam'], 500);
        }
    }

    //View Slot Occupied
    public function ViewAvailableSlot($examdate)
    {
        $slot = Slot::select(
                                'id', 
                                'slotname', 
                                'starttime', 
                                'endtime', 
                                DB::raw('3000 as maxparticipant'),
                                DB::raw('3000 as available'),
                                DB::raw('0 as occupied'),
                            )
                ->where('is_active', 1)->orderBy('id')->get();

        $availableslot = Schedule::select('slotid', 'totalexamparticipant')
                        ->whereDate('examdate', $examdate)
                        ->where('is_deployed', 1)
                        ->get();

        //hitung slot
        foreach ($availableslot as $key => $value) 
        {
            $availableslot[$key]['slotid'] = json_decode($value['slotid']);

            foreach ($value['slotid'] as $slotkey => $slotid) 
            {
                if ($slot[$slotkey]['id'] == $slotid) 
                {
                    $slot[$slotkey]['available'] = $slot[$slotkey]['available'] - $availableslot[$key]['totalexamparticipant'];
                    $slot[$slotkey]['occupied'] = $slot[$slotkey]['occupied'] + $availableslot[$key]['totalexamparticipant'];
                }

                $slot[$slotkey]['slotdate'] = $examdate;
            }
        }

        return $slot;
    }

    //View Course Master
    public function ViewCourseMaster($keywords)
    {
        $coursemaster = SyncLmsCourse::select('course_id', 'subject_code', 'subject_name', 'class');

        $coursemaster->where('subject_name', 'ilike', '%'.$keywords.'%');
        $data = $coursemaster->orderBy('course_id')->get();

        return $data;
    }

    //View Course Parallel
    public function ViewCourseParallel($code)
    {
        $coursemaster = SyncLmsCourse::select('course_id', 'subject_code', 'subject_name', 'class');

        $coursemaster->where('subject_code', $code);
        $data = $coursemaster->orderBy('course_id')->get();

        return $data;
    }

    //View Lecturer Coordinator
    public function ViewLecturerCoordinator($keywords)
    {
        $data = SyncEnrollment::select('userid', 'sync_user.name')
                ->join('sync_user', 'sync_user.userid', '=', 'sync_enrollment.user_id')
                ->where('sync_user.name', 'ilike', '%'.$keywords.'%')
                ->orderBy('sync_user.name')
                ->distinct()
                ->get();

        return $data;
    }

    //View Calendar
    public function ViewCalendar($year)
    {
        $slot = Slot::select(
                                'id', 
                                'slotname', 
                                'starttime', 
                                'endtime', 
                                DB::raw('0 as totalparticipant'),
                            )
                ->where('is_active', 1)
                ->orderBy('id')
                ->get();

        $availableslot = Schedule::select('slotid', 'totalexamparticipant', 'examdate')
                        // ->whereMonth('examdate', $month)
                        ->whereYear('examdate', $year)
                        ->where('is_deployed', 1)
                        ->get();

        foreach ($availableslot as $key => $value) 
        {
            $availableslot[$key]['slotid'] = json_decode($value['slotid']);

            foreach ($value['slotid'] as $slotkey => $slotid) 
            {
                if ($slot[$slotkey]['id'] == $slotid) 
                {
                    $slot[$slotkey]['totalparticipant'] = $slot[$slotkey]['totalparticipant'] + $availableslot[$key]['totalexamparticipant'];

                    //INDICATOR
                    if (($slot[$slotkey]['totalparticipant'] < 1000)) 
                    {
                        $slot[$slotkey]['indicator'] = 'safe';
                    }
                    elseif (($slot[$slotkey]['totalparticipant'] > 1000) && ($slot[$slotkey]['totalparticipant'] < 1501)) 
                    {
                        $slot[$slotkey]['indicator'] = 'low risk';
                    }
                    elseif (($slot[$slotkey]['totalparticipant'] > 1500) && ($slot[$slotkey]['totalparticipant'] < 2001)) 
                    {
                        $slot[$slotkey]['indicator'] = 'medium risk';
                    }
                    else 
                    {
                        $slot[$slotkey]['indicator'] = 'high risk';
                    }
                }

                //start end timestamp
                $slot[$slotkey]['start'] = strtotime($availableslot[$key]['examdate'].' '.$slot[$slotkey]['starttime']);
                $slot[$slotkey]['end'] = strtotime($availableslot[$key]['examdate'].' '.$slot[$slotkey]['endtime']);
            }
        }

        return $slot;
    }

    //View Calendar Detail
    public function ViewCalendarDetail($date)
    {
        $availableslot = Schedule::select(
                                            'schedule_exams.id', 
                                            'schedule_exams.topicname as topic_name',
                                            'schedule_exams.examdate as exam_date',
                                            'schedule_exams.slotid',
                                            'schedule_exams.totalexamparticipant as total_exam_participant',
                                            'schedule_exams.coursemaster as id_course_master',
                                            'schedule_exams.courseparallel',
                                            'schedule_exams.coordinator',
                                            'schedule_exams.is_parallel as use_parallel',
                                            'schedule_exams.is_verified',
                                            'schedule_exams.is_deployed',
                                            'schedule_exams.status'
                                         )
                        ->whereDate('examdate', $date)
                        ->where('is_deployed', 1)
                        ->orderBy('id')
                        ->get();

        foreach ($availableslot as $key => $value) 
        {
            $availableslot[$key]['slotid'] = json_decode($value['slotid']);
            $availableslot[$key]['courseparallel'] = json_decode($value['courseparallel']);

            $min = min(array_keys($availableslot[$key]['slotid']));
            $max = max(array_keys($availableslot[$key]['slotid']));

            $slotmin = Slot::select('starttime')->where('id', $availableslot[$key]['slotid'][$min])->first();
            $slotmax = Slot::select('endtime')->where('id', $availableslot[$key]['slotid'][$max])->first();

            $availableslot[$key]['start_time'] = strtotime($availableslot[$key]['examdate'].' '.$slotmin->starttime);
            $availableslot[$key]['end_time'] = strtotime($availableslot[$key]['examdate'].' '.$slotmax->endtime);

            $availableslot[$key]['exam_start_time'] = null;
            $availableslot[$key]['exam_end_time'] = null;

            $querycourse = SyncLmsCourse::select('subject_code', 'subject_name', 'class')
                                        ->where('course_id', $availableslot[$key]['id_course_master'])
                                        ->first();

            $availableslot[$key]['course_master'] = $querycourse->subject_code.' - '.$querycourse->subject_name.' / '.$querycourse->class;

            if (!is_null($availableslot[$key]['courseparallel'])) 
            {
                $courseparallel = SyncLmsCourse::select('class')->whereIn('course_id', $availableslot[$key]['courseparallel'])->get()->toArray();
                $availableslot[$key]['course_paralel'] = Arr::flatten($courseparallel);
            }
            else
            {
                $availableslot[$key]['course_paralel'] = null;
            }

            $coordinator = SyncUser::select('name as fullname', 'nim_nip as employeeid')
                                    ->where('userid', $availableslot[$key]['coordinator'])
                                    ->first();

            $availableslot[$key]['fullname'] = $coordinator->fullname;
            $availableslot[$key]['employeeid'] = $coordinator->employeeid;
            $availableslot[$key]['topic_activity'] = 1;
        }

        return $availableslot;
    }
}
