<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Course;
use App\Models\Quiz;

class GetQuizList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getquiz';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Quiz List from SINAU';

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
            $response = Http::asForm()->post('https://lms-demo.celoe.org/webservice/rest/server.php', [
                'wstoken' => 'de282c89b7578af73ae88165d48b239b',
                'wsfunction' => 'local_sinau_api_get_quiz_list',
                'moodlewsrestformat' => 'json',
                'course' => $val->course_id,
            ]);

            $decode = json_decode($response, true);

            if ($decode['status'] == true) 
            {
                $data = $decode['data'];

                foreach ($data as $key => $value)
                {
                    $record = Quiz::updateOrCreate(
                                                        [
                                                            'course_id' => $val->course_id,
                                                            'quiz_id' => $value['quiz_id'],
                                                        ],
                                                        [
                                                            'quiz_name' => $value['quiz_name'],
                                                            'timeopen' => $value['timeopen'],
                                                            'timeclose' => $value['timeclose'],
                                                            'timelimit' => $value['timelimit'],
                                                            'attempts' => $value['attempts'],
                                                            'number_questions' => $value['number_questions']
                                                        ]
                                                    );
                }
            }
            $this->info('Finish get quiz from course id: '.$val->course_id);
        }

        // $this->info('Finish get quiz all');
        // return true;
    }
}
