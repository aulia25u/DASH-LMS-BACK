<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Quiz;

class AnalyzeProctoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinau:analyzeproctoring {quiz}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger to analyze proctoring';

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
        $quiz = $this->argument('quiz');

        $response = Http::asForm()->post('https://lms-demo.celoe.org/webservice/rest/server.php', [
                                            'wstoken' => 'de282c89b7578af73ae88165d48b239b',
                                            'wsfunction' => 'local_sinau_api_update_result_proctoring',
                                            'moodlewsrestformat' => 'json',
                                            'quiz_id' => $quiz,
                                        ]);

        $decode = json_decode($response, true);

        if (isset($decode['data'])) 
        {
            $countdata = $decode['data'];
        }
        else
        {
            $countdata = 0;
        }

        $this->info('Analyze Quiz ID: '.$quiz.' - Finish Analyzing '.$countdata.' Data');
        // return true;
    }
}
