<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('curve/show/{id}', ['uses' => 'LoadCurveController@show']);
Route::get('curve/reduce/{id}/{min}/{max}/{dt}', ['uses' => 'LoadCurveController@reduce']);
Route::get('curve/calculate/{c1}/{c2}/{min}/{max}/{dt}/{dl}', ['uses' => 'LoadCurveController@calculate']);

Route::resource('appliance', 'ApplianceController');
Route::resource('sensor', 'SensorController');
Route::resource('curve', 'LoadCurveController');
