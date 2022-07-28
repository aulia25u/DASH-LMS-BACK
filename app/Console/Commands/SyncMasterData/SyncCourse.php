<?php

namespace App\Console\Commands\SyncMasterData;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncLmsCourse;
use App\Models\SyncOffset;

class SyncCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncfromsiterpadu:course';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync course from siterpadu';

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
        $syncoffset = SyncOffset::select('offset', 'last_limit')->where('table', 'sync_lms_course')->first();
        $end = 1;

        if ($syncoffset->last_limit < 100) 
        {
            if ($syncoffset->offset != 1) 
            {
                $offset = $syncoffset->offset - 1;
            }
            else
            {
                $offset = $syncoffset->offset;
            }
            
            $limit = 100;
        }
        else
        {
            $offset = $syncoffset->offset;
            $limit = 100;
        }

        while ($end == 1) 
        {
            $response = Http::asForm()->post(env('SITERPADU_DN').'course', 
            [
                'public_key' => 'CELOE14F28C25E25DF3E5A429CDDEFC901', 
                'offset' => $offset,
                'limit' => $limit,
                // 'semester' => '2122/2',
            ]);

            $decode = json_decode($response, true);

            if (count($decode['list']) < 1) 
            {
                $end = 0;
                break;
            }
            else
            {
                $infooffset = $offset;

                if ($limit != 100) 
                {
                    $limit = 100;
                }

                $offset++;
                SyncOffset::where('table', 'sync_lms_course')->update(['offset' => $offset, 'last_limit' => count($decode['list'])]);
            }

            foreach ($decode['list'] as $key => $value) 
            {
                $record = SyncLmsCourse::updateOrCreate(
                                                        [
                                                            'course_id' => $value['COURSE_ID'],
                                                        ],
                                                        [
                                                            'category_id' => $value['CATEGORY_ID'],
                                                            'semester' => $value['SEMESTER'],
                                                            'class' => $value['CLASS'],
                                                            'subject_code' => $value['SUBJECT_CODE'],
                                                            'subject_name' => $value['SUBJECT_NAME'],
                                                            // 'sync_status' => $value['SYNC_STATUS'],
                                                            'sync_date' => $value['SYNC_DATE'],
                                                            'flag_status' => $value['FLAG_STATUS'],
                                                            'table_owner' => $value['TABLE_OWNER'],
                                                            'table_id' => $value['TABLE_ID'],
                                                            'subject_id' => $value['SUBJECT_ID'],
                                                            // 'user_id' => $value[''],
                                                            // 'user_username' => $value[''],
                                                            // 'employeeid' => $value[''],
                                                            'lecturercode' => $value['LECTURERCODE'],
                                                            // 'employee_name' => $value[''],
                                                            // 'last_sync' => $value[''],
                                                            // 'is_synced' => $value[''],
                                                            // 'is_deleted' => $value[''],
                                                            // 'backup_state' => $value[''],
                                                            // 'backup_path' => $value[''],
                                                            // 'last_backup' => $value[''],
                                                            // 'backup_filename' => $value[''],
                                                            // 'delete_state' => $value[''],
                                                            // 'last_delete' => $value[''],
                                                            // 'course_completion_updated' => $value[''],
                                                            // 'last_completion_attempt' => $value[''],
                                                            // 'course_start' => $value[''],
                                                            // 'course_end' => $value[''],
                                                            // 'course_status' => $value[''],
                                                        ]
                                                    );

                $this->info('Offset: '.$infooffset.'| Number: '.$key);
            }
        }
    }
}
