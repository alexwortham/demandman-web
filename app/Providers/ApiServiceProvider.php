<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
//use App\Services\ApiService;
//use App\Services\DemandManager;
//use App\Services\DatabaseApplianceStore;
//use App\Services\DatabaseRequestStore;
//use App\Services\EventMessenger;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
	$this->app->bind('App\Services\ApiMessenger', 'App\Services\EventMessenger');
	$this->app->bind('App\Services\Manager', 'App\Services\DemandManager');
	$this->app->bind('App\Services\ApplianceStore', 'App\Services\DatabaseApplianceStore');
	$this->app->bind('App\Services\RequestStore', 'App\Services\DatabaseRequestStore');
	$this->app->bind('App\Services\ApplianceApi', 'App\Services\ApiService');
	$this->app->bind('App\Services\Predictor', 'App\Services\CapturedDataPredictor');
	$this->app->bind('App\Services\Analyzer', 'App\Services\BasicAnalyzer');
	$this->app->bind('App\Services\CostCalculator', 'App\Services\BasicCostCalculator');
	$this->app->bind('App\Services\Meter', 'App\Services\MeterService');
    }
}
