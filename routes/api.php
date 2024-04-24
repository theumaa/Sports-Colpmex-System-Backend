<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommonController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function(){
   Route::post('userBooking',[CommonController::class,'userBooking']);
   Route::get('test',(function(){return true;}));
   Route::post('logout',[CommonController::class,'logout']);
   Route::prefix('admin')->middleware('admin')->group(function(){
     Route::get('adminDashboard',[CommonController::class,'adminDashboard']);
     Route::get('users',[CommonController::class,'users']);
     Route::get('completedSessions',[CommonController::class,'completedSessions']);
     Route::get('upcomingSessions',[CommonController::class,'upcomingSessions']);
     Route::get('guestMembers',[CommonController::class,'guestMembers']);
     Route::post('updateStatus',[CommonController::class,'updateStatus']);
     Route::get('testAdmin',[CommonController::class,'testAdmin']);

   });
}); 
Route::post('login',[CommonController::class,'login']);
Route::post('register',[CommonController::class,'register']);
Route::get('getAvailability',[CommonController::class,'getAvailability']);
Route::post('setAvailability',[CommonController::class,'setAvailability']);
Route::get('login',(function(){return 'login first';}))->name('login');
Route::get('error',[CommonController::class,'error'])->name('error');


