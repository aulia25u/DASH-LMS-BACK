<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Analytics;
use Spatie\Analytics\Period;
use Illuminate\Support\Carbon;

use App\Models\Report;

class GAController extends Controller
{
    public function GATest()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::create($startDate, $endDate));
        return $analyticsData->toArray();
    }
    public function GATotalActiveUser()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::create($startDate, $endDate));
        $totalVisitor=0;
        foreach ($analyticsData as $key => $value) {
                $totalVisitor=$totalVisitor+$value['visitors'];
               }       
        $TOTALActiveUser['activeuser']=$totalVisitor;
        return $TOTALActiveUser;
    }
     public function GATestPageView()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::fetchMostVisitedPages(Period::create($startDate, $endDate));
        return $analyticsData->toArray();
    }
    public function GATestPageViewBrowsers()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::fetchTopBrowsers(Period::create($startDate, $endDate));
        return $analyticsData->toArray();
    }
    public function GATestPageViewLocation()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:country'
            ]
        );
         return collect($analyticsData['rows'] ?? [])->map(fn (array $dateRow) => [
            'country' => $dateRow[0],
            'session' => $dateRow[1],
            'pageviews' => (int) $dateRow[2],
        ]);
    }
    public function GATestPageViewLocationCity()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:city'
            ]
        );
        return collect($analyticsData['rows'] ?? [])->map(fn (array $dateRow) => [
            'city' => $dateRow[0],
            'session' => $dateRow[1],
            'pageviews' => (int) $dateRow[2],
        ]);
    }
    public function GATestPageViewDeviceCategory()
    {   
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:deviceCategory'
            ]
        );
        return collect($analyticsData['rows'] ?? [])->map(fn (array $dateRow) => [
            'deviceCategory' => $dateRow[0],
            'session' => $dateRow[1],
            'pageviews' => (int) $dateRow[2],
        ]);
    }
    public function GATestPageViewDeviceCategoryChart()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions, ga:pageviews',
                'dimensions' => 'ga:deviceCategory'
            ]
        );
        $Desktop=0;
        $Tablet=0;
        $Mobile=0;
        foreach ($analyticsData as $value) {
            if ($value[0]=='desktop') {
                $Desktop+=(int) $value[2];
            } if ($value[0]=="tablet") {
                $Tablet+=(int) $value[2];
            } if ($value[0]=="mobile") {
                $Mobile+=(int) $value[2];
            }
        }
        $chart['categories']=['Desktop','Tablet','Mobile'];
        $chart['series']=[$Desktop,$Tablet,$Mobile];

        return $chart;
    }
    public function GATestPageViewOS()
    {
        $startDate = Carbon::now();
        $endDate= Carbon::parse($startDate)->addMinutes();
        $analyticsData = Analytics::performQuery(
            Period::create($startDate, $endDate),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions',
                'dimensions' => 'ga:operatingSystem, ga:operatingSystemVersion,ga:browser,ga:browserVersion'
            ]
        );

        return collect($analyticsData)->toArray();
    }

    //User not participating
    public function MoodleNotParticipating($course, $group)
    {
        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_users_not_participating',
            'moodlewsrestformat' => 'json',
            'course' => $course,
            'group' => $group
        ]);

        $decode = json_decode($response, true);

        $res['total_user_not_participating'] = $decode['data']['total_user'];

        return $res;
    }

    //User participating
    public function MoodleAPIParticipating($quiz, $preview, $state)
    {
        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_users_in_participating',
            'moodlewsrestformat' => 'json',
            'quiz_id' => $quiz,
            'preview' => $preview,
            'state' => $state
        ]);

        return json_decode($response, true);
    }

    public function MoodleParticipating($quiz)
    {
        $inprogress = $this->MoodleAPIParticipating($quiz, 0, 'inprogress');
        $finished = $this->MoodleAPIParticipating($quiz, 0, 'finished');

        $res['total_user_inprogress'] = $inprogress['data']['total_user'];
        $res['total_user_finished'] = $finished['data']['total_user'];

        return $res;
    }

    //User Enroll
    public function MoodleUserEnroll($course)
    {
        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_number_participant',
            'moodlewsrestformat' => 'json',
            'course' => $course
        ]);

        $decode = json_decode($response, true);

        $res['total_user_enrolled'] = $decode['data']['total_user'];

        return $res;
    }

    //Analytic per Quiz Attempt Statistic
    public function MoodleQuizAttemptStatistic($quiz, $start, $end, $group=null)
    {
        if (is_null($group)) 
        {
            $group = 0;
        }

        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_quiz_participant_statistic',
            'moodlewsrestformat' => 'json',
            'quiz_id' => $quiz,
            'timestart' => $start,
            'timeend' => $end,
            'group_id' => $group,
        ]);

        $decode = json_decode($response, true);

        $res['total'] = $decode['data']['participants'];
        $res['finish'] = $decode['data']['finished'];
        $res['inprogress'] = $decode['data']['inprogress'];
        $res['overdue'] = $decode['data']['overdue'];
        $res['notattempt'] = $decode['data']['idle'];
        $res['abandoned'] = $decode['data']['abandoned'];
        $res['blocked'] = $decode['data']['blocked'];

        Report::create([
                            'quiz_id' => $quiz,
                            'group_id' => $group,
                            'interval' => $end,
                            'detail' => json_encode($res),
                       ]);

        return $res;
    }

    //User Active Moodle
    public function MoodleActiveUser($lastaccess)
    {
        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_active_user_statistic',
            'moodlewsrestformat' => 'json',
            'mode' => 'total', //total = hanya count brp x akses, top = detail brp user access
            'lastaccess' => $lastaccess,
            'limit' => 0,
        ]);

        $decode = json_decode($response, true);

        $res['total_active_user'] = $decode['data']['total_user'];

        return $res;
    }

    //User Grouped
    public function MoodleGroupedUser($course)
    {
        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_number_participant',
            'moodlewsrestformat' => 'json',
            'course' => $course,
        ]);

        $decode = json_decode($response, true);

        $res = $decode['data'];

        return $res;
    }

    //Analytic Violation Realtime
    public function MoodleRealtimeViolation($quiz, $start, $end, $group=null)
    {
        if (is_null($group)) 
        {
            $group = 0;
        }

        $response = Http::asForm()->post(env('LMS_DN'), [
            'wstoken' => env('LMS_TOKEN_SINAU'),
            'wsfunction' => 'local_sinau_api_get_exam_violator',
            'moodlewsrestformat' => 'json',
            'quiz_id' => $quiz,
            'timestart' => $start,
            'timeend' => $end,
            'group_id' => $group,
        ]);

        $decode = json_decode($response, true);

        if ($decode['data']['list_user']) 
        {
            foreach ($decode['data']['list_user'] as $key => $value) 
            {
                $res[$key]['user'] = $value['firstname'].' '.$value['lastname'];
                $res[$key]['email'] = $value['email'];
                $res[$key]['attempt'] = $value['attempt_id'];

                $evid = explode(',', $value['screnshots']);

                foreach ($evid as $key2 => $value2) 
                {
                    $res[$key]['evidence'][$key2] = $value2;
                }
            }
        }
        else
        {
            $res = [];
        }

        return $res;
    }
}