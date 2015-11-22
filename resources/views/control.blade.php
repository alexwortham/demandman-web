@extends('layouts.master')

@section('content')
    <div class="page-header">
        <h1>Control Appliances</h1>
    </div>
    @foreach ($appliances as $appliance)
    <h3>{{ $appliance->name }}</h3>
        <a class="btn btn-lg btn-danger" href="{!! URL::route('appliance_circuit', ['id' => $appliance->id, 'state' => 'open'], false) !!}">Open</a>
        <a class="btn btn-lg btn-success" href="{!! URL::route('appliance_circuit', ['id' => $appliance->id, 'state' => 'close'], false) !!}">Close</a>
    @endforeach
@stop

@section('body_post')
<script>
    (function($) {
        $(function() {
                $('a.btn').each(function(i,el){
					$(this).click(function(event) {
						event.preventDefault();
						$.get($(this).attr('href'));
					});
				});
        });
    })(jQuery);

</script>
@stop
