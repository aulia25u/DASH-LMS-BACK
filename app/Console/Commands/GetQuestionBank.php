<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Question;
use App\Models\Course;

class GetQuestionBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:getquestionbank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Question Bank from SINAU';

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
            $response = Http::asForm()->post('https://sinau.seculab.space/webservice/rest/server.php', [
                'wstoken' => '17ab8895ae58be423c98aa2beccc0adf',
                'wsfunction' => 'local_sinau_api_get_question_bank',
                'moodlewsrestformat' => 'json',
                'course' => $val->course_id,
            ]);

            $decode = json_decode($response, true);

            if ($decode['status'] == true) 
            {
                $data = $decode['data'];

                foreach ($data as $key => $value)
                {
                    $record = Question::updateOrCreate(
                                                            [
                                                                'question_id' => $value['question_id'],
                                                            ],
                                                            [
                                                                'question_title' => $value['question_title'],
                                                                'question_content' => $value['question_content'],
                                                                'question_status' => $value['question_status'],
                                                                'question_bank_id' => $value['question_bank_id'],
                                                                'question_bank_name' => $value['question_bank_name'],
                                                                'course_id' => $val->course_id
                                                            ]
                                                    );
                }
            }

            $this->info('Finish get question from course id: '.$val->course_id);
        }

        // $this->info('Finish get quiz');
        // return true;
    }
}
