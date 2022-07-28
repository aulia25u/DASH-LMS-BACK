<?php

namespace App\Console\Commands\SyncLMS;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncEnrollment as DashEnroll;

class Enroll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synclms:enroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Enroll to LMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dataToSync = DashEnroll::select(
                                            'sync_enrollment.user_id', 
                                            'sync_lms_course.table_id',
                                            'sync_enrollment.course_id', 
                                            'sync_enrollment.enrollment_id',
                                            'sync_enrollment.enrollment_type'
                                        )
                    ->join('sync_lms_course', 'sync_lms_course.course_id', '=', 'sync_enrollment.course_id')
                    ->whereNull('sync_enrollment.sync_status')
                    ->where('sync_enrollment.sync_status', '=', 'SYNCED')
                    // ->orWhere('sync_enrollment.sync_status', '=', 'DASH_SYNC')
                    ->orderBy('sync_enrollment.enrollment_id')
                    ->get();

        foreach ($dataToSync as $key => $value) //DOSEN = 3, MAHASISWA = 5
        {
            // if ($value->enrollment_type == 'DOSEN')
            if(strpos($value->enrollment_type, 'DOSEN') !== false) 
            {
                $role = 3;
            } 
            else 
            {
                $role = 5;
            }
            
            $response = Http::asForm()->post(env('LMS_DN'),
                [
                    'wstoken' => env('LMS_TOKEN_SINAU'),
                    'wsfunction' => 'local_sinau_api_enrol_users',
                    'moodlewsrestformat' => 'json',
                    'enrolments[0][userid]' => $value->user_id,
                    'enrolments[0][courseid]' => $value->course_id,
                    'enrolments[0][roleid]' => $role,
                ]);

            $decode = json_decode($response, true);

            if (isset($decode['data'])) 
            {
                if ($decode['data'][0]['status'] == true) 
                {
                    DashEnroll::where('enrollment_id', $value->enrollment_id)->update(['sync_status' => 'SYNCED TO LMS']);

                    $this->info('Success Sync Enrollment to LMS : '.$value->enrollment_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Enrollment/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->enrollment_id.'-'.$decode['data'][0]['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Enrollment/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->enrollment_id.'-'.$decode['exception']);
            }
        }
    }
}
