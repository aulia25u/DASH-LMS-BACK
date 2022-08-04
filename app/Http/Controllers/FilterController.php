<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Group;

class FilterController extends Controller
{
	public function filterCategory()
	{
		$data = Category::orderBy('id')->get();

		return $data;
	}

	public function filterCourse($category)
	{
		$data = Course::where('category_id', $category)->orderBy('id')->get();

		return $data;
	}

	public function filterQuiz($course)
	{
		$data = Quiz::select('quiz_id', 'quiz_name')
					->where('course_id', $course)
					->orderBy('quiz_id')
					->get();

		return $data;
	}

	public function filterGroup($course)
	{
		$data = Group::select('group_id', 'group_name')
					->where('course_id', $course)
					->orderBy('group_id')
					->get();

		return $data;
	}
}