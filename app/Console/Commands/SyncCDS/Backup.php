<?php

namespace App\Console\Commands\SyncCDS;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse as DashCourse;
use App\Models\SyncMasterData\SyncCourse as DashSubject;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synccds:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Backup Course from CDS';

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
        // $dataToSync = DashCourse::ViewCouseDetailBackup();
        $dataToSync = DashSubject::where('sync_status', 'SYNCED')->get();

        foreach ($dataToSync as $key => $value) 
        {
            $response = Http::asForm()->post(env('CDS_DN'), 
                        [
                            'wstoken' => env('CDS_TOKEN'),
                            'wsfunction' => 'local_academic_api_backup_course',
                            'moodlewsrestformat' => 'json',
                            'course' => str_replace(" ", "", trim($value->table_id)),
                            'renew' => 1,
                            'source' => 'cds',
                        ]);

            $decode = json_decode($response, true);

            if (isset($decode['status'])) 
            {
                if ($decode['status'] == true) 
                {
                    DashSubject::where('subject_id', $value->subject_id)->update([
                                                                                    'is_backup' => 1,
                                                                                ]);

                    DashCourse::where('subject_id', $value->subject_id)->update([
                                                                                    'backup_state' => 1,
                                                                                    'backup_path' => $decode['data']['path'],
                                                                                    'last_backup' => now(), 
                                                                                ]);

                    $this->info('Success Backup Course from CDS : '.$value->table_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/BackupCourse/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->table_id.'-'.$decode['data'][0]['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/BackupCourse/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->table_id.'- Error data from Siterpadu');
            }
        }
    }
}
