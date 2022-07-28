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

class ScheduleActionController extends Controller
{
    //Action verify
    public function Verify(Request $request)
    {
        $response = Http::asForm()->post(env('LMS_DN'), 
                            [
                                'wstoken' => env('LMS_TOKEN_SYNC'),
                                'wsfunction' => 'local_academic_api_course_section_check',
                                'moodlewsrestformat' => 'json',
                                'course' => $request->courseid, //source course id
                                'section' => $request->topicname, //source topic name from source course id
                            ]);

        $decode = json_decode($response, true);

        if (isset($decode['status'])) 
        {
            if ($decode['status'] == true) 
            {
                Schedule::where('id', $request->id)->update(['is_verified' => 1, 'status' => 'Waiting For Exam Deployment']);

                return response()->json(['status' => 'Success', 'message' => 'Success verify topic & activity'], 200);
            }
            else
            {
                return response()->json(['status' => 'Failed', 'message' => $decode['exception']], 500);
            }
        }
    }

    //Action deploy
    public function Deploy(Request $request)
    {
        $response = Http::asForm()->post(env('LMS_DN'), 
                            [
                                'wstoken' => env('LMS_TOKEN_SYNC'),
                                'wsfunction' => 'local_academic_api_course_section_check',
                                'moodlewsrestformat' => 'json',
                                'course' => $request->coursemaster, //source course id
                                'section' => $request->topicname, //source topic name from source course id
                                // 'target[0][id]' => $request->coursemaster, //target course id for section import 
                            ]);

        $decode = json_decode($response, true);

        if (isset($decode['status'])) 
        {
            if ($decode['status'] == true) 
            {
                //If has parallel course
                if (isset($request->courseparallel)) 
                {
                    $parallel = explode(',', $request->courseparallel);

                    foreach ($parallel as $key => $value) 
                    {
                        $response = Http::asForm()->post(env('LMS_DN'), 
                                    [
                                        'wstoken' => env('LMS_TOKEN_SYNC'),
                                        'wsfunction' => 'local_academic_api_course_section_import',
                                        'moodlewsrestformat' => 'json',
                                        'source' => $request->coursemaster, //source course id
                                        'section' => $request->topicname, //source topic name from source course id
                                        'target[0][id]' => $value, //target course id for section import 
                                    ]);
                    }
                }
                //End If
                
                Schedule::where('id', $request->id)->update(['is_deployed' => 1, 'status' => 'Done']);

                return response()->json(['status' => 'Success', 'message' => 'Success deploy exam'], 200);
            }
            else
            {
                return response()->json(['status' => 'Failed', 'message' => $decode['exception']], 500);
            }
        }
    }
}
