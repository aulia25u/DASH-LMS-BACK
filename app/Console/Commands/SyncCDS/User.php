<?php

namespace App\Console\Commands\SyncCDS;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncUser as DashUser;

class User extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synccds:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Sync User to CDS';

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
        $dataToSync = DashUser::where('jenis', 'DOSEN/PEGAWAI')->whereNull('sync_status')->orderBy('userid')->get();

        foreach ($dataToSync as $key => $value) 
        {
            $name = explode(' ', trim($value->name));

            if (str_word_count($value->name) > 1) 
            {
                $lastname = explode($name[0], $value->name);
                $lastname = implode(' ', $lastname);
            }
            else
            {
                $lastname = $value->name;
            }

            $response = Http::asForm()->post(env('CDS_DN'), 
                        [
                            'wstoken' => env('CDS_TOKEN_SINAU'),
                            'wsfunction' => 'local_sinau_api_create_users',
                            'moodlewsrestformat' => 'json',
                            'users[0][username]' => str_replace(' ', '', $value->username),
                            'users[0][firstname]' => $name[0],
                            'users[0][lastname]' => $lastname,
                            'users[0][email]' => $value->email,
                            'users[0][password]' => 'dosenUNJANI-22',
                            'users[0][idnumber]' => $value->userid,
                            'users[0][auth]' => 'manual',
                            'users[0][userpicturepath]' => $value->photo,
                            'users[0][userpicture]' => 1,
                        ]);

            $decode = json_decode($response, true);

            if (isset($decode['data'])) 
            {
                if ($decode['data'][0]['status'] == true) 
                {
                    DashUser::where('userid', $value->userid)->update(['sync_status' => 'SYNCED']);

                    $this->info('Success Sync User Lecturer to CDS : '.$value->username);
                }
                else
                {
                    Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/User/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->username.'-'.$decode['data'][0]['exception']);
                }
            }
            else
            {
                Log::build([
                      'driver' => 'single',
                      'path' => storage_path('logs/CDS/User_Master/failed_sync_'.date('Y-m-d').'.log'),
                    ])->info($value->username.'-'.$decode['exception']);
            }
        }
    }
}
