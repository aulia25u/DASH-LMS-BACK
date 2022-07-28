<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Course;
use App\Models\Offset;

class GetCourseList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getcourse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Course List from SINAU';

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

        $set = Offset::select('offset')->where('item', 'course')->first();
        $times = $set->offset;

        while ($condition == 0) 
        {
            $offset = $limit*$times;

            $response = Http::asForm()->post('https://sinau.seculab.space/webservice/rest/server.php', [
                'wstoken' => '17ab8895ae58be423c98aa2beccc0adf',
                'wsfunction' => 'local_sinau_api_get_course_list',
                'moodlewsrestformat' => 'json',
                'limit' => $limit,
                'offset' => $offset,
            ]);

            $decode = json_decode($response, true);

            if ($decode['status'] == true) 
            {
                $data = $decode['data'];

                foreach ($data as $key => $value)
                {
                    if ($value['course_id'] != '') 
                    {
                        $record = Course::updateOrCreate(
                                                            [
                                                                'category_id' => $value['category_id'],
                                                                'course_id' => $value['course_id'],
                                                            ],
                                                            [
                                                                'category_name' => $value['category_name'],
                                                                'course_name' => $value['course_fullname']
                                                            ]
                                                        );   
                    }
                }

                if (count($data) > 100) 
                {
                    Offset::where('item', 'course')->increment('offset');
                    $times++;
                    $this->info('Course Loop: '.$times);
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

        $this->info('Finish get course');
        // return true;
    }
}
