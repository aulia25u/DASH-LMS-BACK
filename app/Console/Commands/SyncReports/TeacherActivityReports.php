<?php

namespace App\Console\Commands\SyncReports;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse as DashCourse;
use App\Models\Reports\TeacherActivity;
use App\Models\Reports\TeacherActivityDetail;

class TeacherActivityReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncreports:teacher_activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Teacher Activity';

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
        $dataToSync = DashCourse::select('course_id')->where('sync_status', 'SYNCED')->get();

        foreach ($dataToSync as $key => $value) 
        {
            $response = Http::asForm()->post(env('LMS_DN'), 
            [
                'wstoken' => env('LMS_TOKEN_SYNC'),
                'wsfunction' => 'local_academic_api_get_teachers_activity_report',
                'moodlewsrestformat' => 'json',
                'courses[0][id]' => $value->course_id,
                'startdate' => time()
            ]);
            // $this->info($response);
            $decode = json_decode($response, true);

            if (isset($decode['status'])) 
            {
                if ($decode['status'] == true) 
                {
                    foreach($decode['data'] as $row){
                        TeacherActivity::updateOrCreate(
                            [
                                'course_id' => $row['course']
                            ],
                            [
                                'number_teachers' => $row['number_teachers'],
                                'start_date' => time(),
                                'sync_status' => 'SYNCED'
                            ]
                        );
                        foreach($row['activity_report'] as $subrow){
                            TeacherActivityDetail::updateOrCreate(
                                [
                                    'course_id' => $row['course'],
                                    'idnumber' => $subrow['idnumber'],
                                    'username' => $subrow['username']
                                ],
                                [
                                    'login' => $subrow['login'],
                                    'grading' => $subrow['grading'],
                                    'discussion' => $subrow['discussion'],
                                    'sync_status' => 'SYNCED'
                                ]
                            );
                        }
                    }

                    $this->info('Success Sync Teacher Activity Report : '.$value->course_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/TeacherActivity/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'-'.$decode['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/TeacherActivity/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'- Error Data not Sync to Teacher Activity Report');
            }
        }
               
    }
}
