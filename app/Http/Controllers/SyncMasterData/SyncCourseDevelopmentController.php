<?php

namespace App\Http\Controllers\SyncMasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncCourse;
use App\Models\SyncMasterData\SyncLmsCourse;

class SyncCourseDevelopmentController extends Controller
{
	public function SyncCourseDevelopment()
	{
		Artisan::call('syncfromsiterpadu:subject');
		Artisan::call('syncfromsiterpadu:course');
		Artisan::call('synccds:course');
		Artisan::call('synccds:enroll');

		return response()->json(['status' => 'Finish', 'message' => 'Finish Sync from Siterpadu'], 200);
	}

	public function ViewCourseDevelopment($faculty=null, $studyprogram=null)
	{
		$data = SyncCourse::ViewCourseDevelopment($faculty, $studyprogram);

		return $data;
	}

	public function UpdateViewCourseDevelopmentBackup($courseid)
	{
		$response = Http::asForm()->post(env('CDS_DN'), 
                        [
                            'wstoken' => env('CDS_TOKEN'),
                            'wsfunction' => 'local_academic_api_backup_course',
                            'moodlewsrestformat' => 'json',
                            'course' => $courseid,
                            'renew' => 1,
                            'source' => 'cds',
                        ]);

        $decode = json_decode($response, true);

        if ($decode['status'] == true) 
        {
        	SyncCourse::where('subject_id', $courseid)->update([
                                                                    'is_backup' => 1,
                                                                ]);
        	
            SyncLmsCourse::where('subject_id', $courseid)->update([
		                                                                        'backup_state' => 1,
		                                                                        'backup_path' => $decode['data']['path'],
		                                                                        'last_backup' => now(), 
		                                                                    ]);

            return response()->json(['status' => 'Success', 'message' => 'Success Sync from Siterpadu'], 200);
        }
        else
        {
            Log::build([
              'driver' => 'single',
              'path' => storage_path('logs/CDS/BackupCourse/failed_sync_'.date('Y-m-d').'.log'),
            ])->info($courseid.'-'.$decode['data'][0]['exception']);

            return response()->json(['status' => 'Failed', 'message' => 'Failed Sync from Siterpadu'], 500);
        }
	}

	public function UpdateViewCourseDevelopmentBackupBulk(Request $request)
	{
		$courseid = explode(',', $request->courseid);

		foreach ($courseid as $key => $value) 
		{
			$response = Http::asForm()->post(env('CDS_DN'), 
                        [
                            'wstoken' => env('CDS_TOKEN'),
                            'wsfunction' => 'local_academic_api_backup_course',
                            'moodlewsrestformat' => 'json',
                            'course' => $value,
                            'renew' => 1,
                            'source' => 'cds',
                        ]);

        	$decode = json_decode($response, true);

        	if (isset($decode['status'])) 
        	{
        		if ($decode['status'] == true) 
		        {
		        	SyncCourse::where('subject_id', $value)->update([
	                                                                    'is_backup' => 1,
	                                                                ]);
		        	
		            SyncLmsCourse::where('subject_id', $value)->update([
				                                                                        'backup_state' => 1,
				                                                                        'backup_path' => $decode['data']['path'],
				                                                                        'last_backup' => now(), 
				                                                                    ]);
		        }
		        else
		        {
		            Log::build([
		              'driver' => 'single',
		              'path' => storage_path('logs/CDS/BackupCourse/failed_sync_'.date('Y-m-d').'.log'),
		            ])->info($value.'-'.$decode['data'][0]['exception']);
		        }
        	}
        	else
        	{
        		Log::build([
		              'driver' => 'single',
		              'path' => storage_path('logs/CDS/BackupCourse/failed_sync_'.date('Y-m-d').'.log'),
		            ])->info($value.'-'.$decode['exception']);
        	}
		}

		return response()->json(['status' => 'Success', 'message' => 'Success Sync from Siterpadu'], 200);
	}

	public function ViewCourseDevelopmentSync($faculty=null, $studyprogram=null)
	{
		$data = SyncCourse::ViewCourseDevelopmentSync($faculty, $studyprogram);

		return $data;
	}
}