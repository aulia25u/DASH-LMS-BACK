<?php

namespace App\Console\Commands\SyncLMS;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse as DashCourse;
use App\Models\SyncMasterData\SyncCourse as DashSubject;

class Course extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synclms:course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Course to LMS';

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
        // $dataToSync = DashCourse::ViewCouseDetailLMS();
        $dataToSync = DashCourse::whereNull('sync_status')->where('backup_state', 1)->get();

        foreach ($dataToSync as $key => $value) 
        {
            $response = Http::asForm()->post(env('LMS_DN'), 
                        [
                            'wstoken' => env('LMS_TOKEN_SYNC'),
                            'wsfunction' => 'local_academic_api_sync_course',
                            'moodlewsrestformat' => 'json',
                            'course' => $value->course_id,
                        ]);

            $decode = json_decode($response, true);

            if (isset($decode['status'])) 
            {
                if ($decode['status'] == true) 
                {
                    DashCourse::where('course_id', $value->course_id)->update(['sync_status' => 'SYNCED']);

                    $this->info('Success Sync Course to LMS : '.$value->course_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Course/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'-'.$decode['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Course/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->course_id.'- Error Data not Sync in LMSCourse');
            }
        }
    }
}
