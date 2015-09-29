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

Route::get('meter/test/{name}/{bus}/{addr}/{min}/{max}', ['uses' => 'LoadMeterController@test']);
Route::get('meter/test/{name}/{bus}/{addr}/{min}/{max}/{val}', ['uses' => 'LoadMeterController@set_meter']);
Route::get('curve/show/{id}', ['uses' => 'LoadCurveController@show']);
Route::get('curve/edit/{id}', ['uses' => 'LoadCurveController@edit']);
Route::post('curve/update/{id}', ['as' => 'curve_update', 'uses' => 'LoadCurveController@update']);
Route::get('curve/reduce/{id}/{min}/{max}/{dt}', ['uses' => 'LoadCurveController@reduce']);
Route::get('curve/calculate/{c1}/{c2}/{min}/{max}/{dt}/{dl}', ['uses' => 'LoadCurveController@calculate']);
Route::get('appliance/{id}/start', ['uses' => 'ApplianceController@start']);
Route::get('appliance/{id}/stop', ['uses' => 'ApplianceController@stop']);
Route::get('appliance/{id}/pause', ['uses' => 'ApplianceController@pause']);
Route::get('appliance/{id}/wake', ['uses' => 'ApplianceController@wake']);
Route::get('appliance/{id}/resume', ['uses' => 'ApplianceController@resume']);

Route::resource('appliance', 'ApplianceController');
Route::resource('sensor', 'SensorController');
Route::resource('curve', 'LoadCurveController');
