<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Question;
use App\Models\Course;
use App\Models\Quiz;

class AnalyticController extends Controller
{
	public function chart($limit=null, $quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal', 
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->orderBy('right_percent', 'desc')
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		if ($data) 
		{
			foreach ($data as $key => $value) 
			{
				$chart['series'][0]['name'] = 'Benar';
				$chart['series'][1]['name'] = 'Salah';
				$chart['series'][2]['name'] = 'Tidak Menjawab';
				$chart['series'][0]['data'][$key] = $value['right_answer'];
				$chart['series'][1]['data'][$key] = $value['wrong_answer'];
				$chart['series'][2]['data'][$key] = $value['null_answer'];
				$chart['categories'][$key] = $value['id_soal'];
			}
		}
		else
		{
			$chart['series'][0]['name'] = 'Benar';
			$chart['series'][1]['name'] = 'Salah';
			$chart['series'][2]['name'] = 'Tidak Menjawab';
			$chart['series'][0]['data'][0] = 0;
			$chart['series'][1]['data'][0] = 0;
			$chart['series'][2]['data'][0] = 0;
			$chart['categories'][0] = $value['id_soal'];
		}

		return $chart;
	}

	public function datatable($paginate=0, $quiz=null, $keywords=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal', 
									'question_content',
									'question_answer',
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->where('question_content', 'like', '%'.$keywords.'%')
						->orderBy('right_percent', 'desc')
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->paginate($paginate);

		return $data;
	}

	//normal table
	public function normaltable($quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal', 
									'question_content',
									'question_answer',
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->orderBy('right_percent', 'desc')
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->get();

		return $data;
	}

	//Soal paling banyak benar
	public function mostrightanswered($limit=null, $quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal',
									'question_content',
									'question_answer',
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->orderBy('right_percent', 'desc')
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		return $data;
	}

	//Soal paling banyak salah
	public function mostwronganswered($limit=null, $quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal',
									'question_content',
									'question_answer',
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		return $data;
	}

	//Soal paling banyak kosong
	public function mostunanswered($limit=null, $quiz=null)
	{
		if ($quiz == null) 
		{
			$cWhere = DB::raw('1');
			$quiz = 1;
		}
		else
		{
			$cWhere = 'quiz_id';
		}

		$data = Analytic::select(
									'question_id as id_soal',
									'question_content',
									'question_answer',
									'user_right_answer as right_answer', 
									'user_wrong_answer as wrong_answer',
									'user_unanswered as null_answer',
									DB::raw('ROUND(( CAST ( user_right_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS right_percent'),
									DB::raw('ROUND(( CAST ( user_wrong_answer AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS wrong_percent'),
									DB::raw('ROUND(( CAST ( user_unanswered AS DECIMAL ) / CAST ( ( user_right_answer + user_wrong_answer + user_unanswered ) AS DECIMAL ) ) * 100, 2) AS null_percent')
								)
						->where($cWhere, $quiz)
						->orderBy('null_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		return $data;
	}

	//Total bank soal & bank soal tampil
	public function loadedQuestion($quiz=null)
	{
		$totalQuestion = Question::join('exam_quiz', 'exam_quiz.course_id', '=', 'exam_questions.course_id')
								->where('exam_quiz.quiz_id', $quiz)
								->count();

		$loadedQuestion = Analytic::where('quiz_id', $quiz)->distinct('question_id')->count();

		$data['categories'][0] = 'Soal Terpakai';
		$data['categories'][1] = 'Soal Tidak Terpakai';
		$data['series'][0] = $loadedQuestion;
		$data['series'][1] = $totalQuestion - $loadedQuestion;

		return $data;
	}

	//Pie Chart Jawaban benar, salah, kosong
	public function rightwrongnull($quiz=null)
	{
		if (is_null($quiz)) 
		{
			$right = Analytic::select(DB::raw('SUM(user_right_answer) AS total'))->first();
			$wrong = Analytic::select(DB::raw('SUM(user_wrong_answer) AS total'))->first();
			$null = Analytic::select(DB::raw('SUM(user_unanswered) AS total'))->first();

			$data['categories'][0] = 'Dijawab Benar';
			$data['categories'][1] = 'Dijawab Salah';
			$data['categories'][2] = 'Tidak Dijawab';

			$data['series'][0] = $right->total;
			$data['series'][1] = $wrong->total;
			$data['series'][2] = $null->total;

			return $data;
		}
		else
		{
			$right = Analytic::select(DB::raw('SUM(user_right_answer) AS total'))->where('quiz_id', $quiz)->first();
			$wrong = Analytic::select(DB::raw('SUM(user_wrong_answer) AS total'))->where('quiz_id', $quiz)->first();
			$null = Analytic::select(DB::raw('SUM(user_unanswered) AS total'))->where('quiz_id', $quiz)->first();

			$data['categories'][0] = 'Dijawab Benar';
			$data['categories'][1] = 'Dijawab Salah';
			$data['categories'][2] = 'Tidak Dijawab';

			$data['series'][0] = (int) $right->total;
			$data['series'][1] = (int) $wrong->total;
			$data['series'][2] = (int) $null->total;

			return $data;
		}
	}
}