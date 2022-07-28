<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\ProctoringResult;

class ProctoringController extends Controller
{
	public function getProctoringResult($quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'id_quiz';
		}

		$data = ProctoringResult::select(
			                                'exam_proctoring_result.firstname',
			                                'exam_proctoring_result.lastname',
			                                'exam_proctoring_result.email',
			                                'quiz_name',
			                                'exam_proctoring_result.user_id'
								  		)
				->where($cWhere, $quiz)
				->join('exam_quiz', 'exam_quiz.quiz_id', '=', 'exam_proctoring_result.id_quiz')
				->distinct('exam_proctoring_result.user_id')
				->get();

		return $data;
	}

	public function getProctoringResultDetail($userid)
	{
		$data = ProctoringResult::select(
											'timestamp',
											'log_id',
											'webcampicture',
											'status',
											'awsscore',
											'awsflag',
											'timemodified'
										)
				->where('user_id', $userid)
				->get();

		return $data;
	}
}