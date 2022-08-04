<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Course;
use App\Models\Group;

class GetGroupList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getgroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Group List from SINAU';

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
        $course = Course::select('course_id')->orderBy('course_id')->get();

        foreach ($course as $crs => $val) 
        {
            $response = Http::asForm()->post(env('LMS_DN'), [
                'wstoken' => env('LMS_TOKEN_SINAU'),
                'wsfunction' => 'local_sinau_api_get_group_list',
                'moodlewsrestformat' => 'json',
                'course' => $val->course_id,
            ]);

            $decode = json_decode($response, true);

            if ($decode['status'] == true) 
            {
                $data = $decode['data'];

                foreach ($data as $key => $value)
                {
                    if (!isset($value['group_section'])) 
                    {
                        $value['group_section'] = NULL;
                    }
                    else
                    {
                        $value['group_section'] = json_encode($value['group_section']);
                    }

                    $record = Group::updateOrCreate(
                                                        [
                                                            'course_id' => $val->course_id,
                                                            'group_id' => $value['group_id'],
                                                        ],
                                                        [
                                                            'group_name' => $value['group_name'],
                                                            'group_desc' => $value['group_desc'],
                                                            'group_section' => $value['group_section'],
                                                        ]
                                                    );
                }
            }
            $this->info('Finish get group from course id: '.$val->course_id);
        }
    }
}
