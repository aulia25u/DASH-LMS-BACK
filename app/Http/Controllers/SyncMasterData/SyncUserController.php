<?php

namespace App\Http\Controllers\SyncMasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncUser;

class SyncUserController extends Controller
{
    public function SyncDashUser()
    {
        Artisan::call('syncfromsiterpadu:user');
        Artisan::call('synccds:user');
        Artisan::call('synclms:user');
        Artisan::call('syncfromsiterpadu:enrollment');

        return response()->json(['status' => 'Finish', 'message' => 'Finish Sync from Siterpadu'], 200);
    }

    public function ViewSyncUser($perpage=10, $jenis=null, $keyword=null)
    {
        $data = SyncUser::ViewSyncUser($perpage, $jenis, $keyword);

        return $data;
    }
}
