<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/profile', 'App\Http\Controllers\UserController@getProfile')->middleware('auth:api');

Route::get('/getexam/{course}/{times}', 'App\Http\Controllers\SchedulerController@getExamAttempt')->middleware('auth:api');
Route::get('/getcategory/{times}', 'App\Http\Controllers\SchedulerController@getCategory')->middleware('auth:api');
Route::get('/getcourse/{times}', 'App\Http\Controllers\SchedulerController@getCourse')->middleware('auth:api');
Route::get('/getquiz/{course}', 'App\Http\Controllers\SchedulerController@getQuiz')->middleware('auth:api');

//Filter Start
Route::get('/filtercategory', 'App\Http\Controllers\FilterController@filterCategory')->middleware('auth:api');
Route::get('/filtercourse/{category}', 'App\Http\Controllers\FilterController@filterCourse')->middleware('auth:api');
Route::get('/filterquiz/{course}', 'App\Http\Controllers\FilterController@filterQuiz')->middleware('auth:api');
//Filter End

Route::get('/exam-analytic/{limit?}/{quiz?}', 'App\Http\Controllers\AnalyticController@chart')->middleware('auth:api');
Route::get('/exam-analytic-table/{paginate?}/{quiz?}/{keywords?}', 'App\Http\Controllers\AnalyticController@datatable')->middleware('auth:api');
Route::get('/exam-analytic-table-normal/{quiz?}', 'App\Http\Controllers\AnalyticController@normaltable')->middleware('auth:api');

Route::get('/question-most-right/{limit?}/{quiz?}', 'App\Http\Controllers\AnalyticController@mostrightanswered')->middleware('auth:api');
Route::get('/question-most-wrong/{limit?}/{quiz?}', 'App\Http\Controllers\AnalyticController@mostwronganswered')->middleware('auth:api');
Route::get('/question-most-null/{limit?}/{quiz?}', 'App\Http\Controllers\AnalyticController@mostunanswered')->middleware('auth:api');

Route::get('/loaded-question/{quiz?}', 'App\Http\Controllers\AnalyticController@loadedQuestion')->middleware('auth:api');
Route::get('/right-wrong-null/{quiz?}', 'App\Http\Controllers\AnalyticController@rightwrongnull')->middleware('auth:api');

Route::get('/pdf/{limit}/{quiz}', 'App\Http\Controllers\PDFController@rightwrongnullreport')->middleware('auth:api');

Route::get('/xls/{quiz}', 'App\Http\Controllers\ExcelController@export')->middleware('auth:api');

//Proctoring

Route::get('/proctoring-result/{quiz?}', 'App\Http\Controllers\ProctoringController@getProctoringResult')->middleware('auth:api');

Route::get('/get-proctoring-result-manual/{course?}', function ($course=null)
{
    $return = Artisan::call('sinau:getproctoringresult', ['course' => $course]);

    return $return;
})->middleware('auth:api');

Route::get('/proctoring-result-detail/{userid}', 'App\Http\Controllers\ProctoringController@getProctoringResultDetail')->middleware('auth:api');

Route::get('/analyze-proctoring/{quiz}', function ($quiz)
{
    $return = Artisan::call('sinau:analyzeproctoring', ['quiz' => $quiz]);

    return true;
})->middleware('auth:api');
Route::get('/gasinau', 'App\Http\Controllers\GAController@GATest')->middleware('auth:api');
Route::get('/gasinauactiveuser', 'App\Http\Controllers\GAController@GATotalActiveUser')->middleware('auth:api');
Route::get('/gasinaupageview', 'App\Http\Controllers\GAController@GATestPageView')->middleware('auth:api');
Route::get('/gasinaupageviewBrowsers', 'App\Http\Controllers\GAController@GATestPageViewBrowsers')->middleware('auth:api');
Route::get('/gasinaupageviewdevicecategory', 'App\Http\Controllers\GAController@GATestPageViewDeviceCategory')->middleware('auth:api');
Route::get('/gasinaupageviewLocation', 'App\Http\Controllers\GAController@GATestPageViewLocation')->middleware('auth:api');
Route::get('/gasinaupageviewLocationcity', 'App\Http\Controllers\GAController@GATestPageViewLocationCity')->middleware('auth:api');
Route::get('/gasinaupageviewos', 'App\Http\Controllers\GAController@GATestPageViewOS')->middleware('auth:api');
Route::get('/gasinaupageviewdevicecategorychart', 'App\Http\Controllers\GAController@GATestPageViewDeviceCategoryChart')->middleware('auth:api');

//Category
Route::get('/syncmasterdata/category', 'App\Http\Controllers\SyncMasterData\SyncCategoryController@SyncCategory');
Route::get('/syncmasterdata/categoryview/{type?}', 'App\Http\Controllers\SyncMasterData\SyncCategoryController@ViewSyncCategory');
Route::get('/syncmasterdata/categoryfilter/faculty', 'App\Http\Controllers\SyncMasterData\SyncCategoryController@ViewFilterFaculty');
Route::get('/syncmasterdata/categoryfilter/studyprogram', 'App\Http\Controllers\SyncMasterData\SyncCategoryController@ViewFilterStudyProgram');
Route::get('/syncmasterdata/categoryfilter/schoolyear', 'App\Http\Controllers\SyncMasterData\SyncCategoryController@ViewFilterSchoolYear');

//Subject
Route::get('/syncmasterdata/coursedevelopment', 'App\Http\Controllers\SyncMasterData\SyncCourseDevelopmentController@SyncCourseDevelopment');
Route::get('/syncmasterdata/coursedevelopmentview/{faculty?}/{studyprogram?}', 'App\Http\Controllers\SyncMasterData\SyncCourseDevelopmentController@ViewCourseDevelopment');
// Route::get('/syncmasterdata/coursedevelopmentview/{faculty?}/{studyprogram?}', 'App\Http\Controllers\SyncMasterData\SyncCourseTeachingController@ViewCourseTeaching');
Route::put('/syncmasterdata/updatecoursedevelopment/{courseid}', 'App\Http\Controllers\SyncMasterData\SyncCourseDevelopmentController@UpdateViewCourseDevelopmentBackup')->middleware('auth:api');
Route::post('/syncmasterdata/updatecoursedevelopment', 'App\Http\Controllers\SyncMasterData\SyncCourseDevelopmentController@UpdateViewCourseDevelopmentBackupBulk');

//Course
Route::get('/syncmasterdata/courseteaching', 'App\Http\Controllers\SyncMasterData\SyncCourseTeachingController@SyncCourseTeaching');
Route::get('/syncmasterdata/coursedevelopmentsyncview/{faculty?}/{studyprogram?}', 'App\Http\Controllers\SyncMasterData\SyncCourseTeachingController@ViewCourseTeaching');

//LMS
Route::get('/lms/viewenrollment/{perpage?}/{faculty?}/{studyprogram?}/{course?}/{role?}/{keyword?}', 'App\Http\Controllers\LMS\LMSController@ViewEnrollment');
Route::get('/lms/enrolllms', 'App\Http\Controllers\LMS\LMSController@EnrollLMS');
Route::get('/lms/synclms', 'App\Http\Controllers\LMS\LMSController@SyncToLMS');

//FILTER
Route::get('/filter/faculty', 'App\Http\Controllers\Filter\FilterController@FilterFaculty');
Route::get('/filter/studyprogram/{parent?}', 'App\Http\Controllers\Filter\FilterController@FilterStudyProgram');
Route::get('/filter/schoolyear/{parent?}', 'App\Http\Controllers\Filter\FilterController@FilterSchoolYear');
Route::get('/filter/subjectcourse/{parent?}', 'App\Http\Controllers\Filter\FilterController@FilterCourse');
Route::get('/filter/roletype', 'App\Http\Controllers\Filter\FilterController@FilterRole');

//USER
Route::get('/dashuser/syncdashuser', 'App\Http\Controllers\SyncMasterData\SyncUserController@SyncDashUser');
Route::get('/dashuser/viewsyncdashuser/{perpage?}/{jenis?}/{keyword?}', 'App\Http\Controllers\SyncMasterData\SyncUserController@ViewSyncUser');

//SLOT
Route::post('/schedule/slot', 'App\Http\Controllers\Schedule\SlotController@CreateSlot');
Route::get('/schedule/slot', 'App\Http\Controllers\Schedule\SlotController@ViewSLot');

//SCHEDULE
Route::post('/schedule/exam', 'App\Http\Controllers\Schedule\ScheduleController@CreateSchedule');
Route::get('/schedule/exam/slot/{examdate}', 'App\Http\Controllers\Schedule\ScheduleController@ViewAvailableSlot');
Route::get('/schedule/exam/coursemaster/{keywords}', 'App\Http\Controllers\Schedule\ScheduleController@ViewCourseMaster');
Route::get('/schedule/exam/courseparallel/{code}', 'App\Http\Controllers\Schedule\ScheduleController@ViewCourseParallel');
Route::get('/schedule/exam/lecturercoordinator/{keywords}', 'App\Http\Controllers\Schedule\ScheduleController@ViewLecturerCoordinator');
Route::get('/schedule/exam/schedulecalendar/{year}', 'App\Http\Controllers\Schedule\ScheduleController@ViewCalendar');
Route::get('/schedule/exam/schedulecalendardetail/{date}', 'App\Http\Controllers\Schedule\ScheduleController@ViewCalendarDetail');

//SCHEDULE VIEW
Route::get('/schedule/exam/verify/view/{perpage?}/{faculty?}/{studyprogram?}/{startdate?}/{enddate?}/{starttime?}/{endtime?}/{status?}/{keywords?}', 'App\Http\Controllers\Schedule\ScheduleMonitorController@ViewTopicActivityVerification');
Route::get('/schedule/exam/deploy/view/{perpage?}/{faculty?}/{studyprogram?}/{startdate?}/{enddate?}/{starttime?}/{endtime?}/{status?}/{keywords?}', 'App\Http\Controllers\Schedule\ScheduleMonitorController@ViewTopicDeploy');

//SCHEDULE ACTION
Route::post('/schedule/action/verify', 'App\Http\Controllers\Schedule\ScheduleActionController@Verify');
Route::post('/schedule/action/deploy', 'App\Http\Controllers\Schedule\ScheduleActionController@Deploy');

//REPORT VIEW
Route::get('/reports/ReportTeacherActivity', 'App\Http\Controllers\Reports\ReportController@ReportTeacherActivity');
Route::get('/reports/ReportTeacherEditing', 'App\Http\Controllers\Reports\ReportController@ReportTeacherEditing');
Route::get('/reports/ReportCourseStats', 'App\Http\Controllers\Reports\ReportController@ReportCourseStats');
