@extends('layouts.master')

@section('content')
{!! Form::open(array('route' => array('curve_update', $curve->id))) !!}
	<div class="form-group">
		{!! Form::label('name', 'Name:', array('class' => 'control-label')) !!}
		{!! Form::text('name', $curve->name, array('class' => 'form-control')) !!}
	</div>
	<div class="form-group">
		{!! Form::label('data', 'Data:') !!}
		{!! Form::textarea('data', $curve->data, array('class' => 'form-control')) !!}
	</div>
	<div class="form-group">
		{!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}
	</div>
{!! Form::close() !!}
@stop
