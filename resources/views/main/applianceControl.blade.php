<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">{{ $appliance->name }}</h3>
  </div>
  <div class="panel-body">
	<div style="width: 25%; display: inline-block;">
    @if ($appliance->type === 'hvac')
	<img src="/images/AC.png" style="height: 100px;" />
    @elseif ($appliance->type === 'wheater')
	<img src="/images/WaterHeater.png" style="height: 100px;" />
    @elseif ($appliance->type === 'dishwash')
	<img src="/images/Dishwasher.png" style="height: 100px;" />
    @elseif ($appliance->type === 'dryer')
	<img src="/images/Dryer.png" style="height: 100px;" />
    @endif
	</div>
	<div style="width: 70%; display: inline-block; padding-left: 1em;">
		<div style="display: inline-block;">
    	<a class="btn btn-success" href="{!! URL::route('appliance_start', ['id' => $appliance->id], false) !!}">
			<span class="glyphicon glyphicon-play"></span>&nbsp;
			Start
		</a>
		</div>
	</div>
  </div>
</div>
