<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use PDF;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;

class PDFController extends Controller
{
	public function rightwrongnullreport($limit, $quiz)
	{
		$right = Analytic::select(
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
						->where('quiz_id', $quiz)
						->orderBy('right_percent', 'desc')
						->orderBy('wrong_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		$wrong = Analytic::select(
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
						->where('quiz_id', $quiz)
						->orderBy('wrong_percent', 'desc')
						->orderBy('right_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		$null = Analytic::select(
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
						->where('quiz_id', $quiz)
						->orderBy('null_percent', 'desc')
						->orderBy('question_id')
						->limit($limit)
						->get();

		$righttr = '';
		$wrongtr = '';
		$nulltr = '';

		//Right
		foreach ($right as $key => $value) 
		{
			$num = $key + 1;

			$righttr .= '<tr style="height: 21px;">';
			$righttr .= '<td style="height: 30px; width: 25px; text-align: center;">'.$num.'</td>';
			$righttr .= '<td style="height: 18px; width: 260px; font-size:10pt;">'.$value->question_content.'</td>';
			$righttr .= '<td style="height: 18px; width: 260px;">'.$value->question_answer.'</td>';
			$righttr .= '<td style="height: 18px; width: 100px;">'.round($value->right_percent, 2).'%'.'<br>'.$value->right_answer.'/'.$value->right_answer + $value->wrong_answer + $value->null_answer.'</td></tr>';
		}

		//Wrong
		foreach ($wrong as $key => $value) 
		{
			$num = $key + 1;

			$wrongtr .= '<tr style="height: 21px;">';
			$wrongtr .= '<td style="height: 30px; width: 25px; text-align: center;">'.$num.'</td>';
			$wrongtr .= '<td style="height: 18px; width: 260px;">'.$value->question_content.'</td>';
			$wrongtr .= '<td style="height: 18px; width: 260px;">'.$value->question_answer.'</td>';
			$wrongtr .= '<td style="height: 18px; width: 100px;">'.round($value->wrong_percent, 2).'%'.'<br>'.$value->wrong_answer.'/'.$value->right_answer + $value->wrong_answer + $value->null_answer.'</td></tr>';
		}

		//Null
		foreach ($null as $key => $value) 
		{
			$num = $key + 1;

			$nulltr .= '<tr style="height: 21px;">';
			$nulltr .= '<td style="height: 30px; width: 25px; text-align: center;">'.$num.'</td>';
			$nulltr .= '<td style="height: 18px; width: 260px;">'.$value->question_content.'</td>';
			$nulltr .= '<td style="height: 18px; width: 260px;">'.$value->question_answer.'</td>';
			$nulltr .= '<td style="height: 18px; width: 100px;">'.round($value->null_percent, 2).'%'.'<br>'.$value->null_answer.'/'.$value->right_answer + $value->wrong_answer + $value->null_answer.'</td></tr>';
		}

		$pdf = PDF::loadHTML('
								<h2 style="color: #2e6c80;"><span style="color: #008000;">'.$limit.' Jawaban Benar Teratas:</span></h2>
									<table class="" border="1">
										<thead>
											<tr style="height: 18px;">
											<th style="height: 18px; width: 25px;">No</th>
											<th style="height: 18px; width: 260px;">Pertanyaan</th>

											<th style="height: 18px; width: 260px;">Jawaban</th>
											<th style="height: 18px; width: 100px;">Persentase</th>
											</tr>
										</thead>
										<tbody>
											'.$righttr.'
										</tbody>
									</table>

									<h2 style="color: #2e6c80;"><span style="color: #008000;">'.$limit.' Jawaban Salah Teratas:</span></h2>
									<table class="" border="1">
										<thead>
											<tr style="height: 18px;">
											<th style="height: 18px; width: 25px;">No</th>
											<th style="height: 18px; width: 260px;">Pertanyaan</th>

											<th style="height: 18px; width: 260px;">Jawaban</th>
											<th style="height: 18px; width: 100px;">Persentase</th>
											</tr>
										</thead>
										<tbody>
											'.$wrongtr.'
										</tbody>
									</table>

									<h2 style="color: #2e6c80;"><span style="color: #008000;">'.$limit.' Jawaban Kosong Teratas:</span></h2>
									<table class="" border="1">
										<thead>
											<tr style="height: 18px;">
											<th style="height: 18px; width: 25px;">No</th>
											<th style="height: 18px; width: 260px;">Pertanyaan</th>

											<th style="height: 18px; width: 260px;">Jawaban</th>
											<th style="height: 18px; width: 100px;">Persentase</th>
											</tr>
										</thead>
										<tbody>
											'.$nulltr.'
										</tbody>
									</table>
							');

    	return $pdf->download('Analytic.pdf');
	}
}