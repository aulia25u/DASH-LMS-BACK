<?php

namespace App\Http\Controllers\SyncMasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncCategory;

class SyncCategoryController extends Controller
{
	public function SyncCategory()
	{
		Artisan::call('syncfromsiterpadu:category');
		Artisan::call('synccds:category');
		Artisan::call('synclms:category');

		return response()->json(['status' => 'Finish', 'message' => 'Finish Sync from Siterpadu'], 200);
	}

	public function ViewSyncCategory($type=null)
	{
		$data = SyncCategory::ViewCategory($type);

		return $data;
	}

	public function ViewFilterFaculty()
	{
		$data = SyncCategory::ViewFilterFaculty();

		return $data;
	}

	public function ViewFilterStudyProgram()
	{
		$data = SyncCategory::ViewFilterStudyProgram();

		return $data;
	}

	public function ViewFilterSchoolYear()
	{
		$data = SyncCategory::ViewFilterSchoolYear();

		return $data;
	}
}