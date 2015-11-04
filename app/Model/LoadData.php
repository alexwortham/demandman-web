<?php

/**
 * Model class for storing a single load datum.
 */
namespace App\Model;

use \Carbon\Carbon;

/**
 * Model class for storing a single load datum.
 *
 * @property \Carbon\Carbon $time The timestamp for this datum.
 * @property double $load The load measured at this point in time.
 * @property \App\Model\AnalogCurrentMonitor $currentMonitor The
 * @property \App\Model\LoadCurve $loadCurve The LoadCurve associated
 * with this model.
 * AnalogCurrentMonitor which made the measurement contained in this datum.
 */
class LoadData extends \Eloquent
{

    /**
     * @param \App\Model\AnalogCurrentMonitor $mon
     * @param Carbon $time
     * @param $load
     * @return \App\Model\LoadData
     */
    public static function createLD($mon,
                                  Carbon $time, $load) {
        $data = new LoadData();
        $data->time = $time;
        $data->load = $load;
        if ($mon !== null) {
            $data->currentMonitor()->associate($mon);
        }

        return $data;
    }

    public function copyLD() {
        $data = new LoadData();
        $data->time = $this->time->copy();
        $data->load = $this->load;
        if ($this->analog_current_monitor != NULL) {
            $data->analog_current_monitor = $this->analog_current_monitor;
        }

        return $data;
    }

    /**
     * @param \App\Model\LoadData $data A value to add to the current load.
     */
    public function addToLoad(LoadData $data) {
        $this->load += $data->load;
    }

    /**
     * Get the LoadCurve which owns this datum.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loadCurve() {
        return $this->belongsTo('App\Model\LoadCurve');
    }

    /**
     * Get the CurrentMonitor which made the measurement contained in this datum.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentMonitor() {
        return $this->belongsTo('App\Model\AnalogCurrentMonitor');
    }
}
