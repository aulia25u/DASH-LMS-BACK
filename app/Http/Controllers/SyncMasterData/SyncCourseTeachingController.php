<?php

namespace App\Http\Controllers\SyncMasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncLmsCourse;

class SyncCourseTeachingController extends Controller
{
	public function SyncCourseTeaching()
	{
		Artisan::call('synclms:course');
		Artisan::call('synclms:enroll');

		return response()->json(['status' => 'Finish', 'message' => 'Finish Sync from Siterpadu'], 200);
	}

	public function ViewCourseTeaching($faculty=null, $studyprogram=null)
	{
		$data = SyncLmsCourse::ViewCourseTeaching($faculty, $studyprogram);

		return $data;
	}
}