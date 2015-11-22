@extends('layouts.master')

@section('content')
    <div class="page-header">
        <h1>Create User Preference</h1>
    </div>
    {!! Form::open(array('route' => 'preferences.store', 'method' => 'POST')) !!}
    <div class="form-group">
        {!! Form::label('name', 'Name:', array('class' => 'control-label')) !!}
        {!! Form::text('name', '', array('class' => 'form-control')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('ui_name', 'UI Name:') !!}
        {!! Form::text('ui_name', '', array('class' => 'form-control')) !!}
    </div>
    <div class="form-group">
        {!! Form::label('value', 'Value:') !!}
        {!! Form::text('value', '', array('class' => 'form-control')) !!}
    <div class="form-group">
        {!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}
    </div>
    {!! Form::close() !!}
@stop
