@extends('layouts.master')

@section('content')
    <div class="page-header">
        <h1>{{ $run->appliance->name }}</h1>
    </div>
    <h3>Captured</h3>
    <div class="row">
    <textarea class="col-md-6" style="height: 300px;">
@foreach($smoothed as $key => $val)
{{ $key }}, {{ $val }}
@endforeach
    </textarea>
    </div>
    <h3>Simulation</h3>
    <div class="row">
    <textarea class="col-md-6" style="height: 300px;">
@foreach($curve as $key => $val)
{{ $key }}, {{ $val }}
@endforeach
    </textarea>
    </div>
@stop
