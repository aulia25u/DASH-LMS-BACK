<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

use App\Models\Analytic;
use App\Models\Course;
use App\Models\Quiz;

class GetQuizAttempts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getquizattempts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Quiz Attempts from SINAU';

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
        $course = Course::select('course_id', 'offset')->orderBy('course_id')->get();

        foreach ($course as $crs => $val) 
        {   
            $limit = 1000;
            $times = 0;
            $condition = 0;
            $this->info('Course ID: '.$val->course_id);

            while ($condition == 0) 
            {
                if ($val->offset != 0) 
                {
                    $times = $val->offset;
                    $offset = $limit*$times;
                }
                else
                {
                    $offset = $limit*$times;
                }

                $response = Http::asForm()->post('https://lms-demo.celoe.org/webservice/rest/server.php', [
                    'wstoken' => 'de282c89b7578af73ae88165d48b239b',
                    'wsfunction' => 'local_sinau_api_get_exam_attempts',
                    'moodlewsrestformat' => 'json',
                    'courseid' => $val->course_id,
                    'limit' => $limit,
                    'offset' => $offset,
                ]);

                $decode = json_decode($response, true);

                if ($decode['status'] == true) 
                {
                    $data = $decode['data'];

                    foreach ($data as $key => $value) 
                    {
                        if (is_null($value['user_answer'])) 
                        {
                            $record = Analytic::updateOrCreate(
                                                                [
                                                                    'course_id' => $value['course_id'],
                                                                    'quiz_id' => $value['quiz_id'],
                                                                    'question_id' => $value['question_id']
                                                                ],
                                                                [
                                                                    'question_content' => $value['question_content'],
                                                                    'question_answer' => $value['question_answer'],
                                                                    // 'user_unanswered' => DB::raw('user_unanswered + 1')
                                                                ]
                                                            );

                            Analytic::where([
                                                'course_id' => $value['course_id'],
                                                'quiz_id' => $value['quiz_id'],
                                                'question_id' => $value['question_id']
                                            ])
                                            ->increment('user_unanswered');
                        }
                        else
                        {
                            if ($value['user_score'] == true) 
                            {
                                $record = Analytic::updateOrCreate(
                                                                [
                                                                    'course_id' => $value['course_id'],
                                                                    'quiz_id' => $value['quiz_id'],
                                                                    'question_id' => $value['question_id']
                                                                ],
                                                                [
                                                                    'question_content' => $value['question_content'],
                                                                    'question_answer' => $value['question_answer'],
                                                                    // 'user_right_answer' => DB::raw('user_right_answer + 1')
                                                                ]
                                                            );

                                Analytic::where([
                                                    'course_id' => $value['course_id'],
                                                    'quiz_id' => $value['quiz_id'],
                                                    'question_id' => $value['question_id']
                                                ])
                                                ->increment('user_right_answer');
                            }
                            else
                            {
                                $record = Analytic::updateOrCreate(
                                                                [
                                                                    'course_id' => $value['course_id'],
                                                                    'quiz_id' => $value['quiz_id'],
                                                                    'question_id' => $value['question_id']
                                                                ],
                                                                [
                                                                    'question_content' => $value['question_content'],
                                                                    'question_answer' => $value['question_answer'],
                                                                    // 'user_wrong_answer' => DB::raw('user_wrong_answer + 1')
                                                                ]
                                                            );

                                Analytic::where([
                                                    'course_id' => $value['course_id'],
                                                    'quiz_id' => $value['quiz_id'],
                                                    'question_id' => $value['question_id']
                                                ])
                                                ->increment('user_wrong_answer');
                            }
                        }
                    }

                    if (count($data) > 1000) 
                    {
                        Course::where('course_id', $val->course_id)->increment('offset');
                        $times++;
                        $this->info('Course ID: '.$val->course_id.' Loop: '.$times);
                    }
                    else
                    {
                        $condition++; 
                    }
                }
                else
                {
                    $condition++;

                    // Course::where('course_id', $val->course_id)->update(['offset' => $times]);
                }
            }
        }

        $this->info('Finish get quiz attempts');
        return true;
    }
}
