<?php

/**
 * Model class for storing a single load datum.
 */
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for storing a single load datum.
 *
 * @property \Carbon\Carbon $time The timestamp for this datum.
 * @property double $load The load measured at this point in time.
 * @property \App\Model\LoadCurve $loadCurve The LoadCurve which owns this datum.
 * @property \App\Model\AnalogCurrentMonitor The AnalogCurrentMonitor which made
 * the measurement contained in this datum.
 */
class LoadData extends Model
{
    /**
     * Get the LoadCurve which owns this datum.
     *
     * @return \App\Model\LoadCurve The LoadCurve which owns this datum.
     */
    public function loadCurve() {
        return $this->belongsTo('App\Model\LoadCurve');
    }

    /**
     * Get the CurrentMonitor which made the measurement contained in this datum.
     *
     * @return \App\Model\AnalogCurrentMonitor The CurrentMonitor which made
     * this measurement contained in this datum.
     */
    public function currentMonitor() {
        return $this->belongsTo('App\Model\AnalogCurrentMonitor');
    }
}
