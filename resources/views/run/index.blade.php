@extends('layouts.master')

@section('content')
<h1>Appliance Run History</h1>

<ul class="list-unstyled">
@foreach ($runs as $run)
	<li><a href="/run/{{ $run->id }}/live">{{ $run->appliance->name }} at {{$run->created_at}}</a></li>
@endforeach
</ul>

@stop
