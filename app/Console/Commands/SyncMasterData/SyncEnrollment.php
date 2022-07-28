<?php

namespace App\Console\Commands\SyncMasterData;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncEnrollment as SyncEnroll;
use App\Models\SyncOffset;

class SyncEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncfromsiterpadu:enrollment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command sync category from siterpadu';

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
        $syncoffset = SyncOffset::select('offset', 'last_limit')->where('table', 'sync_enrollment')->first();
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
            $response = Http::asForm()->post(env('SITERPADU_DN').'enrollment', 
            [
                'public_key' => env('SITERPADU_TOKEN'), 
                'offset' => $offset,
                'limit' => $limit,
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
                SyncOffset::where('table', 'sync_enrollment')->update(['offset' => $offset, 'last_limit' => count($decode['list'])]);
            }

            foreach ($decode['list'] as $key => $value) 
            {
                $record = SyncEnroll::updateOrCreate(
                                                            [
                                                                'enrollment_id' => $value['ENROLLMENT_ID'],
                                                            ],
                                                            [
                                                                'course_id' => $value['COURSE_ID'],
                                                                'enrollment_type' => $value['ENROLLMENT_TYPE'],
                                                                'class' => $value['CLASS'],
                                                                'user_id' => $value['USER_ID'],
                                                                'user_username' => $value['USER_USERNAME'],
                                                                'user_email' => $value['USER_EMAIL'],
                                                                'enrollment_status' => $value['ENROLLMENT_STATUS'],
                                                                'sync_status' => $value['SYNC_STATUS'],
                                                                'sync_date' => $value['SYNC_DATE'],
                                                                'flag_status' => $value['FLAG_STATUS'],
                                                                'table_owner' => $value['TABLE_OWNER'],
                                                                'table_id' => $value['TABLE_ID']
                                                            ]
                                                        );

                $this->info('Offset: '.$infooffset.'| Number: '.$key);
            }
        }
    }
}
