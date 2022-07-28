<?php

namespace App\Console\Commands\SyncLMS;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncCategory as DashCategory;

class Category extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synclms:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync Category to LMS';

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
        $raworder = "   CASE 
                            WHEN category_type = 'FACULTY' THEN 1
                            WHEN category_type = 'STUDYPROGRAM' THEN 2
                            WHEN category_type = 'SCHOOLYEAR' THEN 3
                        END";

        $dataToSync = DashCategory::where('sync_status', 'SYNCED')->orderBy(DB::raw($raworder))->get();

        foreach ($dataToSync as $key => $value) 
        {
            // $parentdata = DashCategory::select('table_id')->where('category_id', $value->cateogry_parent_id)->first();

            // $parentid = is_null($parentdata) ? null : $parentdata->table_id;

            // $this->info($value->category_name.'-'.$parentid);

            $response = Http::asForm()->post(env('LMS_DN'), 
                        [
                            'wstoken' => env('LMS_TOKEN_SINAU'),
                            'wsfunction' => 'local_sinau_api_create_categories',
                            'moodlewsrestformat' => 'json',
                            'categories[0][name]' => $value->category_name,
                            'categories[0][parent]' => $value->cateogry_parent_id,
                            'categories[0][idnumber]' => $value->category_id,
                            'categories[0][description]' => $value->category_type,
                        ]);
            $decode = json_decode($response, true);

            if (isset($decode['data'])) 
            {
                if ($decode['data'][0]['status'] == true) 
                {
                    DashCategory::where('category_id', $value->category_id)->update(['sync_status' => 'SYNCED TO LMS']);

                    $this->info('Success Sync Category to LMS : '.$value->category_id);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Category/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->category_id.'-'.$decode['data'][0]['exception']);
                }
            }  
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/LMS/Category/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->category_id.'-'.$decode['exception']);
            }            
        }
    }
}
