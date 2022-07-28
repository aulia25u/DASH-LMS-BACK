<?php

namespace App\Http\Controllers;

use App\Exports\AnalyticExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Analytic;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;

class ExcelController extends Controller 
{
    public function export($quiz) 
    {
        $data = Quiz::select('exam_quiz.quiz_name', 'exam_quiz.timeopen', 'exam_quiz.timeclose', 'exam_courses.category_name', 'exam_courses.course_name')
                    ->join('exam_courses', 'exam_courses.course_id', '=', 'exam_quiz.course_id')
                    ->where('quiz_id', $quiz)
                    ->first();

        return Excel::download(new AnalyticExport($data, $quiz), 'Analytic.xlsx');
    }
}