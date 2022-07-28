<?php

namespace App\Console\Commands\SyncMasterData;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncCourse;
use App\Models\SyncOffset;

class SyncSubject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncfromsiterpadu:subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync subject from siterpadu';

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
        $syncoffset = SyncOffset::select('offset', 'last_limit')->where('table', 'sync_course')->first();
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
            $response = Http::asForm()->post(env('SITERPADU_DN').'subject', 
            [
                'public_key' => 'CELOE14F28C25E25DF3E5A429CDDEFC901', 
                'offset' => $offset,
                'limit' => $limit,
                // 'year' => '2017',
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
                SyncOffset::where('table', 'sync_course')->update(['offset' => $offset, 'last_limit' => count($decode['list'])]);
            }

            foreach ($decode['list'] as $key => $value) 
            {
                $record = SyncCourse::updateOrCreate(
                                                        [
                                                            'subject_id' => $value['SUBJECT_ID'],
                                                        ],
                                                        [
                                                            'subject_code' => $value['SUBJECT_CODE'],
                                                            'subject_name' => $value['SUBJECT_NAME'],
                                                            'subject_type' => $value['SUBJECT_TYPE'],
                                                            'subject_ppdu' => $value['SUBJECT_PPDU'],
                                                            'credit' => $value['CREDIT'],
                                                            'curriculum_year' => $value['CURRICULUM_YEAR'],
                                                            'sync_by' => $value['SYNC_BY'],
                                                            // 'sync_status' => $value['SYNC_STATUS'],
                                                            'sync_date' => $value['SYNC_DATE'],
                                                            'flag_status' => $value['FLAG_STATUS'],
                                                            'table_owner' => $value['TABLE_OWNER'],
                                                            'table_id' => $value['TABLE_ID'],
                                                            'category_id' => $value['CATEGORY_ID'],
                                                            'studyprogramid' => $value['STUDYPROGRAMID'],
                                                            'approve_status' => $value['APPROVE_STATUS'],
                                                            'approve_date' => $value['APPROVE_DATE'],
                                                            'notes' => $value['NOTES'],
                                                            'approve_by' => $value['APPROVE_BY'],
                                                            'input_by' => $value['INPUT_BY'],
                                                            'input_date' => $value['INPUT_DATE'],
                                                            // 'last_backup' => $value[''],
                                                            // 'is_backup' => $value[''],
                                                            // 'is_manual_insert' => $value[''],
                                                            // 'is_deleted' => $value[''],
                                                            // 'subject_desc' => $value[''],
                                                            // 'subject_status' => $value[''],
                                                        ]
                                                    );

                $this->info('Offset: '.$infooffset.'| Number: '.$key);
            }
        }
    }
}
