<?php

namespace App\Exports;

use App\Models\Analytic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;

class AnalyticExport implements FromCollection, WithHeadings, WithTitle, WithStartRow
{
    /**
    * @return \Illuminate\Support\Collection
    */
    function __construct(Object $data, $quiz)
    {
        $this->data = $data;
        $this->quiz = $quiz;
    }

    public function collection()
    {
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
                        ->where('quiz_id', $this->quiz)
                        ->orderBy('right_percent', 'desc')
                        ->orderBy('wrong_percent', 'desc')
                        ->orderBy('question_id')
                        ->get();

        return $data->collect();
    }

    public function title(): string
    {
        $data = $this->data;

        return $data->quiz_name;
    }

    public function headings(): array
    {
        $data = $this->data;

        if ($data->timeopen != 0) 
        {
            $dateopen = date('d-M-Y', $data->timeopen);
        }
        else
        {
            $dateopen = '-';
        }
        
        return [['Category: '.$data->category_name, 'Course: '.$data->course_name, 'Quiz: '.$data->quiz_name, 'Waktu: '.$dateopen],
        ['ID SOAL', 'PERTANYAAN', 'JAWABAN', 'DIJAWAB BENAR', 'DIJAWAB SALAH', 'TIDAK DIJAWAB', 'PERSENTASE DIJAWAB BENAR', 'PERSENTASE DIJAWAB SALAH', 'PERSENTASE TIDAK DIJAWAB']];
    }

    public function startRow(): int
    {
        return 12;
    }
}
