<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Demand Management</title>
		{!! Html::style('css/bootstrap.min.css') !!}
		{!! Html::style('css/starter-template.css') !!}
		{!! Html::style('css/jquery-ui.min.css') !!}
		{!! Html::style('css/jquery-ui.smoothness.min.css') !!}
		@yield('styles')
	</head>
	<body>
		<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle Navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand navbar-brand-lg" href="/">Demand Man</a>
				</div>
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
					<!--	<li><a href="/curve/calculate/2/1/0/120/1/30">Demo 1</a></li>
						<li><a href="/curve/create">Add Curve</a></li>
						<li><a href="/curve">List Curves</a></li>
						<li><a href="{!! URL::route('run.index') !!}">Run History</a></li>
						-->
					</ul>
				</div>
			</div>
		</div>
		
		<div class="container">
		
			<div class="starter-template">
<!--				<g:if test="${ flash?.message }">
					<g:alert message="${ flash.message }" />
				</g:if>-->
				@yield('content')
			</div>
			
		</div>
		{!! Html::script('js/jquery-2.1.4.min.js') !!}
		{!! Html::script('js/bootstrap.min.js') !!}
		{!! Html::script('js/jquery-ui.min.js') !!}
		@yield('body_post')
	</body>
</html>
