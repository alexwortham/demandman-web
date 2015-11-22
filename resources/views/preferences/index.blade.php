@extends('layouts.master')

@section('content')
    <h1>Preferences</h1>

    <ul class="list-unstyled">
        @foreach ($preferences as $preference)
            <li><a href="{{ route('preferences.edit', ['id' => $preference->id]) }}">{{ $preference->ui_name }}</a></li>
        @endforeach
    </ul>

@stop
