<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Start User//

Route::get('authors', 'api\UserController@index');
Route::get('author/{id}','api\UserController@show');
Route::get('posts/author/{id}','api\UserController@posts');
Route::get('comments/author/{id}','api\UserController@comments');

//End User//

//Start Comments//
Route::get('comment/{id}','api\CommentController@show');
//End Comments//

//Start Post//
Route::get('posts','api\PostController@index');
Route::get('post/{id}','api\PostController@show');
Route::get('comments/post/{id}','api\PostController@comments');
Route::get('categories','api\CategoryController@index');
Route::get('posts/category/{id}','api\CategoryController@show');
//End Post//
Route::post('register','api\UserController@store');
Route::post( 'token','api\UserController@token' );

Route::middleware('auth:api')->group(function (){
    Route::post('update-user','api\UserController@renew');
    Route::post('update-avatar','api\UserController@addav');
    Route::get('delete-avatar','api\UserController@delav');
    Route::post('post','api\PostController@store');
    Route::post('update/post/{id}','api\PostController@update');
    Route::delete('post/{id}','api\PostController@destroy');
    Route::post('comment/post/{id}','api\CommentController@store');
    Route::post('update-comment/{id}','api\CommentController@update');
    Route::delete('comment/{id}','api\CommentController@destroy');
    Route::post('vote/{id}','api\PostController@vote');
});
