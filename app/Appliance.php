<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appliance extends Model {

	protected $table = 'appliances';
	public $timestamps = true;

	public function sensor()
	{
		return $this->hasOne('Sensor');
	}

}