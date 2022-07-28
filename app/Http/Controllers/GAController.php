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
}