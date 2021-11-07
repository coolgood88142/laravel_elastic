<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/add', function () {
    return view('add');
});

Route::get('/addIndex', 'ArticlesController@addIndex');

Route::get('/showArticles', 'ArticlesController@searchArticles');

Route::post('/searchArticles', 'ArticlesController@searchArticles');

Route::get('/testSearch', 'ArticlesController@searchArticles');

Route::get('/addArticles', 'ArticlesController@addArticles');

Route::get('/testDelete', 'ArticlesController@deleteArticles');

