<?php

namespace App\Console\Commands\SyncMasterData;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\SyncMasterData\SyncUser as SUser;
use App\Models\SyncOffset;

class SyncUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncfromsiterpadu:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command user category from siterpadu';

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
        $syncoffset = SyncOffset::select('offset', 'last_limit')->where('table', 'sync_user')->first();
        $end = 1;

        if ($syncoffset->last_limit < 100) 
        {
            if ($syncoffset->offset != 1) 
            {
                $offset = $syncoffset->offset - 1;
            }
            else
            {
                $offset = $syncoffset->offset;
            }
            
            $limit = 100;
        }
        else
        {
            $offset = $syncoffset->offset;
            $limit = 100;
        }

        while ($end == 1) 
        {
            $response = Http::asForm()->post(env('SITERPADU_DN').'users', 
            [
                'public_key' => env('SITERPADU_TOKEN'), 
                'offset' => $offset,
                'limit' => $limit,
            ]);

            $decode = json_decode($response, true);

            if (isset($decode['list'])) 
            {
                if (count($decode['list']) < 1) 
                {
                    $end = 0;
                    break;
                }
                else
                {
                    $infooffset = $offset;

                    if ($limit != 100) 
                    {
                        $limit = 100;
                    }

                    $offset++;
                    SyncOffset::where('table', 'sync_user')->update(['offset' => $offset, 'last_limit' => count($decode['list'])]);
                }

                foreach ($decode['list'] as $key => $value) 
                {
                    $record = SUser::updateOrCreate(
                                                                [
                                                                    'userid' => $value['userid'],
                                                                ],
                                                                [
                                                                    'name' => $value['name'],
                                                                    'nim_nip' => $value['nim_nip'],
                                                                    'username' => $value['username'],
                                                                    'email' => $value['email'],
                                                                    'jenis' => $value['jenis'],
                                                                    'photo' => $value['photo']
                                                                ]
                                                            );

                    $this->info('Offset: '.$infooffset.'| Number: '.$key);
                }
            }
            else
            {
                $this->info('Finish');
            }
        }
    }
}
