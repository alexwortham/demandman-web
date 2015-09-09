@extends('layouts.master')

@section('content')
<h1>Load Curves</h1>

<ul class="list-unstyled">
@foreach ($curves as $curve)
	<li><a href="/curve/show/{{ $curve->id }}">{{ $curve->name }}</a></li>
@endforeach
</ul>

@stop
