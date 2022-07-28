<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

use App\Models\Sync\Course;
use App\Models\Sync\Student;
use App\Models\Sync\Lecturer;

class SyncEnrollment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:enrollment {role}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Enrollment SITERPADU -> SINAU REG';

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
        if ($this->argument('role') == '3') //Lecturer
        {
            $data = Lecturer::whereNull('date_sync')->get();

            foreach ($data as $key => $value) 
            {
                $this->info($key.'-'.$value->userid.'-'.$value->courseid);
                $response = Http::asForm()->post(
                    'https://sinau.unjani.ac.id/webservice/rest/server.php', 
                [
                    'wstoken' => '17ab8895ae58be423c98aa2beccc0adf',
                    'wsfunction' => 'local_sinau_api_enrol_users',
                    'moodlewsrestformat' => 'json',
                    'enrolments[0][userid]' => $value->userid,
                    'enrolments[0][courseid]' => $value->courseid,
                    'enrolments[0][roleid]' => 3,
                ]);

                $decode = json_decode($response, true);

                if ($decode['data'][0]['status'] == true) 
                {
                    Lecturer::where('userid', $value->userid)->where('courseid', $value->courseid)->update(['date_sync' => now()->timestamp]);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Lecturer/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->userid.'-'.$decode['data'][0]['exception']);
                }
            }

            $this->info('Finish Enroll Lecturer');
        }
        elseif ($this->argument('role') == '5') //Student
        {
            $data = Student::whereNull('date_sync')->get();

            foreach ($data as $key => $value) 
            {
                $this->info($key.'-'.$value->userid.'-'.$value->courseid);
                $response = Http::asForm()->post(
                    'https://sinau.unjani.ac.id/webservice/rest/server.php', 
                [
                    'wstoken' => '17ab8895ae58be423c98aa2beccc0adf',
                    'wsfunction' => 'local_sinau_api_enrol_users',
                    'moodlewsrestformat' => 'json',
                    'enrolments[0][userid]' => $value->userid,
                    'enrolments[0][courseid]' => $value->courseid,
                    'enrolments[0][roleid]' => 5,
                ]);

                $decode = json_decode($response, true);

                if ($decode['data'][0]['status'] == true) 
                {
                    Student::where('userid', $value->userid)->where('courseid', $value->courseid)->update(['date_sync' => now()->timestamp]);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/Student/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->userid.'-'.$decode['data'][0]['exception']);
                }
            }

            $this->info('Finish Enroll Student');
        }
        else
        {
            $this->info('Invalid Role');
        }

        return 0;
    }
}
