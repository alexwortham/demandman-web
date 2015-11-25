@extends('layouts.master')

@section('content')
    <div class="page-header">
        <h1>Update User Preference</h1>
    </div>
    {!! Form::open(array('route' => array('preferences.update', $preference->id), 'method' => 'put')) !!}
    <div class="form-group">
        {!! Form::label('name', 'Name:', array('class' => 'control-label')) !!}
        {!! Form::text('name', $preference->name, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('ui_name', 'UI Name:') !!}
        {!! Form::text('ui_name', $preference->ui_name, array('class' => 'form-control')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('value', 'Value:') !!}
        {!! Form::text('value', $preference->value, array('class' => 'form-control')) !!}
        <div class="form-group">
            {!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}
        </div>
    {!! Form::close() !!}
@stop
