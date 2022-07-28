<?php

namespace App\Http\Controllers\LMS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncEnrollment;

class LMSController extends Controller
{
	public function ViewEnrollment($perpage=10, $faculty=null, $studyprogram=null, $course=null, $role=null, $keyword=null)
	{
		$data = SyncEnrollment::ViewEnrollment($perpage, $faculty, $studyprogram, $course, $role, $keyword);

		return $data;
	}

	public function EnrollLMS()
	{
		//Artisan::command
		Artisan::call('syncfromsiterpadu:enrollment');
		Artisan::call('synccds:enroll');
		Artisan::call('synclms:enroll');

		return response()->json(['status' => 'Success', 'message' => 'Success to Enroll'], 200);
	}

	public function SyncToLMS()
	{
		Artisan::call('synclms:course');

		return response()->json(['status' => 'Success', 'message' => 'Success to Sync To Content/Sinau'], 200);
	}
}