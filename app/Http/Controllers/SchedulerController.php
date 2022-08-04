<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;

class SchedulerController extends Controller
{
	//Start Get Exam Attempt Manually
	public function getExamAttempt($course, $times)
	{
		$condition = 0;

		while ($condition == 0) 
		{
			$limit = 1000;
			$offset = $limit*$times;

			$response = Http::asForm()->post(env('SINAU_DN'), [
			    'wstoken' => env('SINAU_TOKEN'),
				'wsfunction' => 'local_sinau_api_get_exam_attempts',
				'moodlewsrestformat' => 'json',
				'courseid' => $course,
				'limit' => $limit,
				'offset' => $offset,
			]);

			$decode = json_decode($response, true);

			if ($decode['status'] == true) 
			{
				$data = $decode['data'];

				foreach ($data as $key => $value) 
				{
					if (is_null($value['user_answer'])) 
					{
						$record = Analytic::updateOrCreate(
															[
														    	'course_id' => $value['course_id'],
														    	'quiz_id' => $value['quiz_id'],
														    	'question_id' => $value['question_id']
															],
															[
														    	'question_content' => $value['question_content'],
								                                'question_answer' => $value['question_answer'],
								                                'user_unanswered' => DB::raw('user_unanswered + 1')
															]
														);
					}
					else
					{
						if ($value['user_score'] == true) 
						{
							$record = Analytic::updateOrCreate(
															[
														    	'course_id' => $value['course_id'],
														    	'quiz_id' => $value['quiz_id'],
														    	'question_id' => $value['question_id']
															],
															[
														    	'question_content' => $value['question_content'],
								                                'question_answer' => $value['question_answer'],
								                                'user_right_answer' => DB::raw('user_right_answer + 1')
															]
														);
						}
						else
						{
							$record = Analytic::updateOrCreate(
															[
														    	'course_id' => $value['course_id'],
														    	'quiz_id' => $value['quiz_id'],
														    	'question_id' => $value['question_id']
															],
															[
														    	'question_content' => $value['question_content'],
								                                'question_answer' => $value['question_answer'],
								                                'user_wrong_answer' => DB::raw('user_wrong_answer + 1')
															]
														);
						}
					}
				}

				$times++;
				// Log::info($times);
				$condition++; //End Every 1000 attempt
			}
			else
			{
				$condition++;
			}
		}

		return 'done';
	}
	//End Get Exam Attemp Manually

	//Start Get Category
	public function getCategory($times)
	{
		$condition = 0;

		while ($condition == 0) 
		{
			$limit = 1000;
			$offset = $limit*$times;

			$response = Http::asForm()->post(env('SINAU_DN'), [
			    'wstoken' => env('SINAU_TOKEN'),
				'wsfunction' => 'local_sinau_api_get_category_list',
				'moodlewsrestformat' => 'json',
				'limit' => $limit,
				'offset' => $offset,
			]);

			$decode = json_decode($response, true);

			if ($decode['status'] == true) 
			{
				$data = $decode['data'];

				foreach ($data as $key => $value)
				{
					$record = Category::updateOrCreate(
															[
														    	'category_id' => $value['category_id'],
															],
															[
														    	'category_name' => $value['category_name'],
										                        'category_desc' => $value['category_desc'],
										                        'category_parent' => $value['category_parent']
															]
														);
				}

				$times++;
				// Log::info($times);
			}
			else
			{
				$condition++;
			}
		}

		return 'done';
	}
	//End Get Category

	//Start Get Course
	public function getCourse($times)
	{
		$condition = 0;

		while ($condition == 0) 
		{
			$limit = 1000;
			$offset = $limit*$times;

			$response = Http::asForm()->post(env('SINAU_DN'), [
			    'wstoken' => env('SINAU_TOKEN'),
				'wsfunction' => 'local_sinau_api_get_course_list',
				'moodlewsrestformat' => 'json',
				'limit' => $limit,
				'offset' => $offset,
			]);

			$decode = json_decode($response, true);

			if ($decode['status'] == true) 
			{
				$data = $decode['data'];

				foreach ($data as $key => $value)
				{
					$record = Course::updateOrCreate(
															[
														    	'category_id' => $value['category_id'],
														    	'course_id' => $value['course_id'],
															],
															[
														    	'category_name' => $value['category_name'],
										                        'course_name' => $value['course_fullname']
															]
														);
				}

				$times++;
				// Log::info($times);
			}
			else
			{
				$condition++;
			}
		}

		return 'done';
	}
	//End Get Course

	//Start Get Quiz
	public function getQuiz($course)
	{
		$response = Http::asForm()->post(env('SINAU_DN'), [
		    'wstoken' => env('SINAU_TOKEN'),
			'wsfunction' => 'local_sinau_api_get_quiz_list',
			'moodlewsrestformat' => 'json',
			'course' => $course,
		]);

		$decode = json_decode($response, true);

		if ($decode['status'] == true) 
		{
			$data = $decode['data'];

			foreach ($data as $key => $value)
			{
				$record = Quiz::updateOrCreate(
													[
														'course_id' => $course,
												    	'quiz_id' => $value['quiz_id'],
													],
													[
												    	'quiz_name' => $value['quiz_name'],
								                        'timeopen' => $value['timeopen'],
								                        'timeclose' => $value['timeclose'],
								                        'timelimit' => $value['timelimit'],
								                        'attempts' => $value['attempts'],
								                        'number_questions' => $value['number_questions']
													]
												);
			}
		}
		else
		{
			return 'done-failed';
		}

		return 'done';
	}
	//End Get Quiz
}