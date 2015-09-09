{!! Form::open(array('route' => 'route.name', 'method' => 'POST')) !!}
	<ul>
		<li>
			{!! Form::label('name', 'Name:') !!}
			{!! Form::text('name') !!}
		</li>
		<li>
			{!! Form::label('mode', 'Mode:') !!}
			{!! Form::text('mode') !!}
		</li>
		<li>
			{!! Form::label('pin', 'Pin:') !!}
			{!! Form::text('pin') !!}
		</li>
		<li>
			{!! Form::submit() !!}
		</li>
	</ul>
{!! Form::close() !!}