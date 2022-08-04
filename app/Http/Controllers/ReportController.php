<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Http;

use PDF;

class ReportController extends Controller 
{
	public function ReportMonitoringInterval($quiz, $group=null)
	{
		if (is_null($group)) 
        {
            $group = 0;
        }

        $data = Report::select(
        						'interval',
        						"detail->total as total",
        						"detail->finish as finish",
        						"detail->inprogress as inprogress",
        						"detail->overdue as overdue",
        						"detail->notattempt as notattempt",
        						"detail->abandoned as abandoned",
        						"detail->blocked as blocked",
    						  )
        		->where('quiz_id', $quiz)
        		->where('group_id', $group)
        		->get();

       	return $data;
	}

	public function ReportMonitoringDetail($quiz, $group=null)
	{
		if (is_null($group)) 
        {
            $group = 0;
        }

        $response = Http::asForm()->post(env('SINAU_DN'), [
            'wstoken' => env('SINAU_TOKEN'),
            'wsfunction' => 'local_sinau_api_get_quiz_participant_status',
            'moodlewsrestformat' => 'json',
            'quiz_id' => $quiz,
            'group_id' => $group,
        ]);

        $decode = json_decode($response, true);

        $isi = '';

        if (isset($decode['data']['list_user'])) 
        {
        	foreach ($decode['data']['list_user'] as $key => $value) 
			{
				$num = $key + 1;

				$isi .= '<tr>';
				$isi .= '<td style="height: 18px; width: 25px">'.$num.'</td>';
				$isi .= '<td style="height: 18px; width: 130px">'.$value['username'].'</td>';
				$isi .= '<td style="height: 18px; width: 100px">'.$value['attempt_id'].'</td>';
				$isi .= '<td style="height: 18px; width: 130px">'.$value['firstname'].' '.$value['lastname'].'</td>';
				$isi .= '<td style="height: 18px; width: 130px">'.$value['email'].'</td>';
				$isi .= '<td style="height: 18px; width: 130px">'.$value['state'].'</td></tr>';
			}
        }

        $pdf = PDF::loadHTML('
								<h2 style="color: #2e6c80;"><span">Laporan Monitoring Realtime</span></h2>
									<table class="" border="1">
										<thead>
											<tr>
											<th style="height: 18px; width: 25px">No.</th>
											<th style="height: 18px; width: 130px">Username</th>
											<th style="height: 18px; width: 100px">Attempt</th>
											<th style="height: 18px; width: 130px">Nama</th>
											<th style="height: 18px; width: 130px">email</th>
											<th style="height: 18px; width: 130px">Status</th>
											</tr>
										</thead>
										<tbody>
											'.$isi.'
										</tbody>
									</table>
							');

        return $pdf->download('ReportMonitoring.pdf');
	}
}