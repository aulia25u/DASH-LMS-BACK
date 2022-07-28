<?php

namespace App\Console\Commands\SyncReports;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse as DashCourse;
use App\Models\Reports\TeacherEditing;
use App\Models\Reports\TeacherEditingDetail;

class TeacherEditingReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncreports:teacher_editing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Teacher Editing';

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
                'wsfunction' => 'local_academic_api_get_teachers_editing_report',
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
                        TeacherEditing::updateOrCreate(
                            [
                                'course_id' => $row['course']
                            ],
                            [
                                'number_teachers' => $row['number_teachers'],
                                'start_date' => time(),
                                'sync_status' => 'SYNCED'
                            ]
                        );
                        foreach($row['editing_report'] as $subrow){
                           TeacherEditingDetail::updateOrCreate(
                                [
                                    'course_id' => $row['course'],
                                    'idnumber' => $subrow['idnumber'],
                                    'username' => $subrow['username']
                                ],
                                [
                                    'section' => $subrow['section'],
                                    'label' => $subrow['label'],
                                    'page' => $subrow['page'],
                                    'forum' => $subrow['forum'],
                                    'assignment' => $subrow['assignment'],
                                    'quiz' => $subrow['quiz'],
                                    'attendance' => $subrow['attendance'],
                                    'question' => $subrow['question'],
                                    'sync_status' => 'SYNCED'
                                ]
                            );
                        }
                    }

                    $this->info('Success Sync Teacher Editing Report : '.$value->course_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/TeacherEditing/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'-'.$decode['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/TeacherEditing/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'- Error Data not Sync to Teacher Editing Report');
            }
        }
               
    }
}
