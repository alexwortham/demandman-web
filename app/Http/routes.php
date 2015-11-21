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

Route::get('/', ['uses' => 'MainController@index']);

Route::get('meter/test/{bus}/{addr}/{inc}', ['uses' => 'LoadMeterController@test']);
Route::get('meter/test/{bus}/{addr}/{inc}/{val}', ['uses' => 'LoadMeterController@set_meter']);
Route::get('curve/show/{id}', ['uses' => 'LoadCurveController@show']);
Route::get('curve/edit/{id}', ['uses' => 'LoadCurveController@edit']);
Route::post('curve/update/{id}', ['as' => 'curve_update', 'uses' => 'LoadCurveController@update']);
Route::get('curve/reduce/{id}/{min}/{max}/{dt}', ['uses' => 'LoadCurveController@reduce']);
Route::get('curve/calculate/{c1}/{c2}/{min}/{max}/{dt}/{dl}', ['uses' => 'LoadCurveController@calculate']);
Route::get('appliance/{id}/start', ['as' => 'appliance_start', 'uses' => 'ApplianceController@start']);
Route::get('appliance/{id}/stop', ['as' => 'appliance_stop', 'uses' => 'ApplianceController@stop']);
Route::get('appliance/{id}/pause', ['as' => 'appliance_pause', 'uses' => 'ApplianceController@pause']);
Route::get('appliance/{id}/wake', ['as' => 'appliance_wake', 'uses' => 'ApplianceController@wake']);
Route::get('appliance/{id}/resume', ['as' => 'appliance_resume', 'uses' => 'ApplianceController@resume']);
Route::get('run/{id}/live', ['as' => 'run.live', 'uses' => 'RunController@live']);
Route::get('run/{id}/data', ['as' => 'run.data', 'uses' => 'RunController@data']);
Route::get('run/{id}/live/{since}', ['as' => 'run.live', 'uses' => 'RunController@liveUpdate']);

Route::resource('appliance', 'ApplianceController');
Route::resource('sensor', 'SensorController');
Route::resource('curve', 'LoadCurveController');
Route::resource('run', 'RunController');
