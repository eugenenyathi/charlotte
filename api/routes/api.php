<?php

use Illuminate\Http\Request;
use App\Http\Controllers\VEngine;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UtilsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VAuditController;
use App\Http\Controllers\VTesterController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\GenerateRequestsController;
use App\Http\Controllers\RoommatePreferenceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/test/{studentID}', [TestController::class, 'index']);

//TODO: REMOVE ITS FOR TESTING
Route::get('/v1/test/request/status/{studentID}', [RequestsController::class, 'requestStatus']);


Route::prefix('/v1/utils')->controller(UtilsController::class)->group(function () {
    Route::get('/spread', 'studentSpread');
    Route::get('/random/student', 'randomStudent');
    Route::get('/profile/{studentID}', 'studentProfile');
});



//charlotte-auto app
Route::get("/v1/auto-app/generate-requests", [GenerateRequestsController::class, 'init']);
Route::get("/v1/auto-app/v-tests/{scenario}", [VTesterController::class, 'init']);

Route::prefix('/v1/auto-app')->controller(MiscellaneousController::class)->group(function () {
    Route::get('/stats', 'stats');
    Route::get('/destroy', 'destroyAll');
    Route::get('/clear', 'clearAll');
    Route::get('/revert', 'reverseProcessedRequests');
    Route::get('/clear/rooms', 'clearRooms');
    Route::get('/distribution/{level}', 'countGenderFirstSameLevelStudents');
    Route::get('/count', 'count');
    Route::get('/pull', 'pullStudents');
    Route::get('/res', 'fakeRes');
});

//v-engine
Route::prefix('/v1/v-engine')->controller(VEngine::class)->group(function () {
    Route::get('/run', 'init');
    Route::get('/audit', 'audit');
});

//v-audit
Route::get('/v1/v-audit', [VAuditController::class, 'auditInit']);


Route::prefix('/v1')->controller(AuthController::class)->group(function () {
    Route::post("/validate", 'validateCredentials');
    Route::post("/login", 'login');
    Route::get("/logout/{studentID}", 'logout');
    Route::post("/destroy/{studentID}", 'destroy');
});


//protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/v1/token', function () {
        return response('valid token');
    });

    Route::prefix('/v1')->controller(HomeController::class)->group(function () {
        Route::get("/dashboard/1/{studentID}", 'dashboardReminder');
        Route::get("/dashboard/2/{studentID}", 'dashboardAside');
        Route::get("/profile/{studentID}", 'profile');
        Route::get("/residence/{studentID}", 'residence');

        //changing the password
        // Route::post("/password/verify", 'verifyCurrentPassword');
        // Route::patch("/password/update", 'updatePassword');
    });

    Route::prefix('/v1')->controller(SearchController::class)->group(function () {
        Route::post("/search/{student_id}", 'search')->where('student_id', '^L0\d*');
        Route::get("/match/{student_id}", 'match')->where('studentID', '^L0\d{6}[A-Z]{1}$');
    });

    Route::prefix('/v1')->controller(RoommatePreferenceController::class)->group(function () {
        Route::get('/roommate_preference/{studentID}', 'get')->where('studentID', '^L0\d{6}[A-Z]{1}$');
        Route::post('/roommate_preference', 'create');
        Route::put('/roommate_preference', 'update');
    });

    Route::prefix('/v1/request')->controller(RequestsController::class)->group(function () {
        Route::get('/status/{studentID}', 'requestStatus');
        Route::post("", 'createRequest');
        Route::put("", 'updateRequest');
        Route::patch("/response", 'roommateResponse');
        Route::patch("/response/revert/{studentID}", 'revertResponse');
        Route::delete("/destroy/{studentID}", 'destroyRequest');
    });

    // logout
    Route::get("/v1/logout/{studentID}", [AuthController::class, 'logout']);
});
