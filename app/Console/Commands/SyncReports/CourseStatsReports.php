<?php

namespace App\Console\Commands\SyncReports;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse as DashCourse;
use App\Models\Reports\CourseStats;

class CourseStatsReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncreports:course_stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Course Stats';

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
                'wsfunction' => 'local_academic_api_get_course_dev_stats',
                'moodlewsrestformat' => 'json',
                'courses[0][id]' => $value->course_id,
            ]);
            // $this->info($response);
            $decode = json_decode($response, true);

            if (isset($decode['status'])) 
            {
                if ($decode['status'] == true) 
                {
                    foreach($decode['data'] as $row){
                        CourseStats::updateOrCreate(
                            [
                                'course_id' => $row['course']
                            ],
                            [
                                'subject_code' => $row['subject_code'],
                                'subject_name' => $row['subject_name'],
                                'percent_profile' => $row['percent_profile'],
                                'percent_topic' => $row['percent_topic'],
                                'list_sections' => $row['list_sections'],
                                'section' => $row['section'],
                                'file' => $row['file'],
                                'assignment' => $row['assignment'],
                                'quiz' => $row['quiz'],
                                'url' => $row['url'],
                                'forum' => $row['forum'],
                                'sync_status' => 'SYNCED'
                            ]
                        );
                    }

                    $this->info('Success Sync Course Stats Report : '.$value->course_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/CourseStats/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'-'.$decode['exception']);
                    $this->info('Failed Sync Course Stats Report : '.$value->course_id);

                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Reports/CourseStats/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'- Error Data not Sync to Course Stats Report');
                    $this->info('Failed2 Sync Course Stats Report : '.$value->course_id);

            }
        }
               
    }
}
