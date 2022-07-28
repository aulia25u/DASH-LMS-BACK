<?php

namespace App\Console\Commands\SyncCDS;

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
    protected $signature = 'synccds:course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Course to CDS';

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
        // $dataToSync = DashSubject::ViewCouseDetail();
        $dataToSync = DashSubject::select(
                                            'sync_course.table_id',
                                            'sync_course.subject_id',
                                            'sync_course.subject_code',
                                            'sync_course.subject_name',
                                            'sync_course.category_id'
                                         )
                    ->join('sync_category', 'sync_category.category_id', '=', 'sync_course.category_id')
                    ->whereNull('sync_course.sync_status')
                    ->get();

        foreach ($dataToSync as $key => $value) 
        {
            $response = Http::asForm()->post(env('CDS_DN'), 
                        [
                            'wstoken' => env('CDS_TOKEN_SINAU'),
                            'wsfunction' => 'local_sinau_api_create_courses',
                            'moodlewsrestformat' => 'json',
                            'courses[0][shortname]' => trim($value->subject_code) .'-'.$value->subject_id,
                            'courses[0][fullname]' => trim($value->subject_name),
                            'courses[0][idnumber]' => $value->subject_id,
                            'courses[0][categoryid]' => $value->category_id,
                            'courses[0][sections][0][name]' => 'Pertemuan 1',
                            'courses[0][sections][1][name]' => 'Pertemuan 2',
                            'courses[0][sections][2][name]' => 'Pertemuan 3',
                            'courses[0][sections][3][name]' => 'Pertemuan 4',
                            'courses[0][sections][4][name]' => 'Pertemuan 5',
                            'courses[0][sections][5][name]' => 'Pertemuan 6',
                            'courses[0][sections][6][name]' => 'Pertemuan 7',
                            'courses[0][sections][7][name]' => 'Pertemuan 8',
                            'courses[0][sections][8][name]' => 'Ujian Tengah Semester',
                            'courses[0][sections][9][name]' => 'Pertemuan 9',
                            'courses[0][sections][10][name]' => 'Pertemuan 10',
                            'courses[0][sections][11][name]' => 'Pertemuan 11',
                            'courses[0][sections][12][name]' => 'Pertemuan 12',
                            'courses[0][sections][13][name]' => 'Pertemuan 13',
                            'courses[0][sections][14][name]' => 'Pertemuan 14',
                            'courses[0][sections][15][name]' => 'Pertemuan 15',
                            'courses[0][sections][16][name]' => 'Pertemuan 16',
                            'courses[0][sections][17][name]' => 'Ujian Akhir Semester',
                        ]);

            $decode = json_decode($response, true);

            if (isset($decode['data'])) 
            {
                if ($decode['data'][0]['status'] == true) 
                {
                    // DashCourse::where('course_id', $value->course_id)->update(['sync_status' => 'SYNCED', 'is_synced' => true, 'last_sync' => now()]);
                    DashSubject::where('subject_id', $value->subject_id)->update(['sync_status' => 'SYNCED']);

                    $this->info('Success Sync Course to CDS : '.$value->subject_id);
                }
                else
                {
                    $this->info('Success Sync Course to CDS : '.$value->subject_id.'-'.$decode['data'][0]['exception']);

                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/Course/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->subject_id.'-'.$decode['data'][0]['exception']);
                }
            }
            else
            {
                $this->info('Success Sync Course to CDS : '.$value->subject_id.'-'.$decode['exception']);

                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/Course/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->subject_id.'-'.$decode['exception']);
            }
        }
    }
}
