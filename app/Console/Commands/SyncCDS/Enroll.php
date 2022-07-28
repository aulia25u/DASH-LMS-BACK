<?php

namespace App\Console\Commands\SyncCDS;

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
    protected $signature = 'synccds:enroll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Enroll to CDS';

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
                                            'sync_enrollment.enrollment_id',
                                            'sync_enrollment.course_id',
                                            'sync_lms_course.table_id',
                                            'sync_enrollment.user_id',
                                            'sync_enrollment.enrollment_type'
                                        )
                    ->join('sync_lms_course', 'sync_lms_course.course_id', '=', 'sync_enrollment.course_id')
                    ->where('sync_enrollment.enrollment_type', 'ilike', '%DOSEN%')
                    ->whereNull('sync_enrollment.sync_status')
                    // ->orWhere('sync_enrollment.sync_status', '=', 'DASH_SYNC')
                    ->orderBy('sync_enrollment.enrollment_id')
                    ->get();

        // print_r($dataToSync); die;

        foreach ($dataToSync as $key => $value) //DOSEN = 3, MAHASISWA = 5
        {
            // if ($value->enrollment_type == 'DOSEN') 
            if(str_contains($value->enrollment_type, 'DOSEN'))
            {
                $role = 3;
            } 
            else 
            {
                $role = 5;
            }
            
            $response = Http::asForm()->post(env('CDS_DN'),
                [
                    'wstoken' => env('CDS_TOKEN_SINAU'),
                    'wsfunction' => 'local_sinau_api_enrol_users',
                    'moodlewsrestformat' => 'json',
                    'enrolments[0][userid]' => $value->user_id,
                    'enrolments[0][courseid]' => $value->course_id,
                    'enrolments[0][roleid]' => $role,
                ]);

            $decode = json_decode($response, true);

            if (isset($decode['data'][0])) 
            {
                if ($decode['data'][0]['status'] == true) 
                {
                    DashEnroll::where('enrollment_id', $value->enrollment_id)->update(['sync_status' => 'SYNCED']);

                    $this->info('Success Sync Enrollment to CDS : '.$value->enrollment_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/Enrollment/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->enrollment_id.'-'.$decode['data'][0]['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/Enrollment/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->enrollment_id.'-'.$decode['exception']);
            }
        }
    }
}
