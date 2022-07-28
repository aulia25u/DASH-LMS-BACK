<?php

namespace App\Http\Controllers\Filter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

use App\Models\SyncMasterData\SyncCategory;
use App\Models\SyncMasterData\SyncCourse;
use App\Models\SyncMasterData\SyncLmsCourse;
use App\Models\SyncMasterData\SyncEnrollment;
use App\Models\SyncMasterData\SyncUser;

class FilterController extends Controller
{
	public function FilterFaculty()
	{
		$data = SyncCategory::FilterFaculty();

		return $data;
	}

	public function FilterStudyProgram($parent=null)
	{
		$data = SyncCategory::FilterStudyProgram($parent);

		return $data;
	}

	public function FilterSchoolYear($parent=null)
	{
		$data = SyncCategory::FilterSchoolYear($parent);

		return $data;
	}

	public function FilterCourse($parent=null)
	{
		$data = SyncCourse::FilterCourse($parent);

		return $data;
	}

	public function FilterRole()
	{
		$data = SyncUser::select('jenis')->orderBy('jenis')->distinct()->get();

		return $data;
	}
}