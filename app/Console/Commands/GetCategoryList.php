<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Category;
use App\Models\Offset;

class GetCategoryList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getcategory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Category List from SINAU';

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
        $condition = 0;
        $limit = 100;

        $set = Offset::select('offset')->where('item', 'category')->first();
        $times = $set->offset;

        while ($condition == 0) 
        {
            $offset = $limit*$times;

            $response = Http::asForm()->post('https://lms-demo.celoe.org/webservice/rest/server.php', [
                'wstoken' => 'de282c89b7578af73ae88165d48b239b',
                'wsfunction' => 'local_sinau_api_get_category_list',
                'moodlewsrestformat' => 'json',
                'limit' => $limit,
                'offset' => $offset,
            ]);

            $decode = json_decode($response, true);

            $this->info($decode['status']);

            if ($decode['status'] == true) 
            {
                $data = $decode['data'];

                foreach ($data as $key => $value)
                {
                    $record = Category::updateOrCreate(
                                                            [
                                                                'category_id' => $value['category_id'],
                                                            ],
                                                            [
                                                                'category_name' => $value['category_name'],
                                                                'category_desc' => $value['category_desc'],
                                                                'category_parent' => $value['category_parent']
                                                            ]
                                                        );
                }

                if (count($data) > 100) 
                {
                    Offset::where('item', 'category')->increment('offset');
                    $times++;
                    $this->info('Category Loop: '.$times);
                }
                else
                {
                    $condition++; 
                }
                
            }
            else
            {
                $condition++;
            }
        }
        
        $this->info('Finish get category');
        // return true;
    }
}
