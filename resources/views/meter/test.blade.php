@extends('layouts.master')

@section('styles')
{!! Html::style('css/jquery-ui.min.css') !!}
{!! Html::style('css/jquery-ui.theme.min.css') !!}
@stop

@section('content')
<div class="page-header">
	<h1>Load Meter Test</h1>
</div>
<div class="row">
	<div class="col-md-1" style="text-align: right;">{{ $min }}</div>
	<div id="slider" class="col-md-4"></div>
	<div class="col-md-1">{{ $max }}</div>
</div>
@stop

@section('body_post')
{!! Html::script('js/jquery-ui.min.js') !!}
<script>

(function($) {

	$(function() {
		var url = window.location.href;
		$('#slider').slider({
			min: {{ $min }},
			max: {{ $max }},
			change: function ( event, ui ) {
				$.get(url + '/' + ui.value);
			}
		});
	});
})(jQuery);
</script>
@stop
