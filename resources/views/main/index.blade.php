@extends('layouts.master')

@section('content')
<h2>Usage History</h2>
<p>Placeholder</p>

<h2>Control Panel</h2>
<div class="row">
@foreach ($appliances as $appliance)
	<div class="col-md-6">
	@include('main/applianceControl')
	</div>
@endforeach
</div>
@stop

@section('body_post')
	<script>
		(function($){
			$(function() {
				$('a.btn-success').each(function(i,el){
					$(this).click(function(event) {
						event.preventDefault();
						$.get($(this).attr('href'));
					});
				});
			});
		})(jQuery);

	</script>
@stop
