<?php

namespace App\Console\Commands\SyncMasterData;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncCategory as DashCategory;
use App\Models\SyncOffset;

class SyncCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncfromsiterpadu:category';

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
        $syncoffset = SyncOffset::select('offset', 'last_limit')->where('table', 'sync_category')->first();
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
            $response = Http::asForm()->post(env('SITERPADU_DN').'category', 
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
                SyncOffset::where('table', 'sync_category')->update(['offset' => $offset, 'last_limit' => count($decode['list'])]);
            }

            foreach ($decode['list'] as $key => $value) 
            {
                $record = DashCategory::updateOrCreate(
                                                            [
                                                                'category_id' => $value['CATEGORY_ID'],
                                                            ],
                                                            [
                                                                'category_name' => $value['CATEGORY_NAME'],
                                                                // 'shortname' => null,
                                                                'category_type' => $value['CATEGORY_TYPE'],
                                                                'initial_studyprogram' => $value['INITIAL_STUDYPROGRAM'],
                                                                'cateogry_parent_id' => $value['CATEGORY_PARENT_ID'],
                                                                'group_leader' => $value['GROUP_LEADER'],
                                                                'sync_date' => $value['SYNC_DATE'],
                                                                'sync_status' => $value['CATEGORY_TYPE'] == 'SCHOOLYEAR' ? 'SYNCED' : $value['SYNC_STATUS'],
                                                                'flag_status' => $value['FLAG_STATUS'],
                                                                'table_owner' => $value['TABLE_OWNER'],
                                                                'table_id' => $value['TABLE_ID'],
                                                                'updated_date' => $value['UPDATED_DATE'],
                                                                'updated_id' => $value['UPDATED_ID'],
                                                                // 'is_manual_insert' => null,
                                                                // 'category_desc' => null,
                                                                // 'category_status'  => null,
                                                            ]
                                                        );

                $this->info('Offset: '.$infooffset.'| Number: '.$key);
            }
        }
    }
}
