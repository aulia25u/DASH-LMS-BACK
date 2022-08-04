<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Course;
use App\Models\ProctoringResult;

class GetProctoringResult extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getproctoringresult {course}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Proctoring Result from SINAU';

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
            $course = $this->argument('course');
            $courseDetail = Course::select('course_id', 'proctoring_offset')->where('course_id', $course)->first();
            
            $condition = 0;
            $limit = 1000;
            $times = $courseDetail->proctoring_offset;
            $offset = $limit*$times;

            while ($condition == 0) 
            {
                $response = Http::asForm()->post('https://lms-demo.celoe.org/webservice/rest/server.php', [
                    'wstoken' => 'de282c89b7578af73ae88165d48b239b',
                    'wsfunction' => 'local_sinau_api_get_result_proctoring',
                    'moodlewsrestformat' => 'json',
                    'course' => $course,
                    'threshold' => 80,
                    'flag' => 2,
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                $decode = json_decode($response, true);

                if ($decode['status'] == true) 
                {
                    $data = $decode['data'];

                    foreach ($data as $key => $value)
                    {
                        $record = ProctoringResult::updateOrCreate(
                                                                    [
                                                                        'email' => $value['email'],
                                                                        'log_id' => $value['id'],
                                                                        'firstname' => $value['firstname'],
                                                                        'lastname' => $value['lastname'],
                                                                        'user_id' => $value['userid'],
                                                                    ],
                                                                    [
                                                                        'quiz_id' => $value['quizid'],
                                                                        'timestamp' => $value['timeattempt'],
                                                                        'course_id' => $course,
                                                                        'id_quiz' => $value['id_quiz'],
                                                                        'webcampicture' => $value['webcampicture'],
                                                                        'status' => $value['status'],
                                                                        'awsscore' => $value['awsscore'],
                                                                        'awsflag' => $value['awsflag'],
                                                                        'timemodified'  => $value['timemodified'],
                                                                    ]
                                                                );
                    }

                    if (count($data) > 1000) 
                    {
                        Course::where('course_id', $course)->increment('proctoring_offset');
                        $times++;
                        $this->info('Proctoring course loop: '.$times);
                    }
                    else
                    {
                        $condition++;
                        $this->info('Finish get prcotoring result from course id: '.$course);
                    }
                }
                else
                {
                    $condition++;
                    $this->info('Finish get prcotoring result from course id: '.$course);
                }
            }
        return true;
    }
}
