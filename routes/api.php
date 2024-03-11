<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware'=>'auth:api'],function(){
    Route::post('post',[PostController::class,'store']);
    Route::get('posts',[PostController::class,'index']);
    Route::post('post/{id}',[PostController::class,'update']);
    Route::delete('post/{id}',[PostController::class,'destroy']);
    Route::get('post/{id}',[PostController::class,'show']);
    //like
    Route::get('likes/{id}',[LikeController::class,'getlike']);
    Route::post('togglelik/{id}',[LikeController::class,'togglelike']);
    //comments
    Route::get('comments/{id}',[CommentController::class,'show']);
    Route::post('comment/{id}',[CommentController::class,'store']);
    Route::put('comment/{id}',[CommentController::class,'update']);
    Route::delete('comment/{id}',[CommentController::class,'destroy']);
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

   Route::post('login',[AuthController::class,'login']);
   Route::post('logout',[AuthController::class,'logout']);
   Route::get('refresh',[AuthController::class,'refresh']);
   Route::get('me',[AuthController::class,'me']);
   Route::post('register',[AuthController::class,'register']);
   Route::post('updateProfile/{id}',[AuthController::class,'update']);
});
