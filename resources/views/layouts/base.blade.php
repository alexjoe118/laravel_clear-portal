<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="csrf-token" content="{{ csrf_token() }}">
			<title>{{ globalSettings('title') ?? config('app.name', 'Clear Portal') }}</title>

			@if (globalSettings('favicon'))
				<link rel="shortcut icon" type="image/x-icon" href="{{ globalSettings('favicon') }}" />
			@endif
			<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">

			@yield('styles')

			{!! globalSettings('head_scripts') !!}

			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvWL732lit9LUjKuQ9s3ynLwdkEKgd2dg&libraries=places"></script>
			<script src="http://code.jquery.com/jquery-2.1.1.js"></script>

	</head>
	<body>
		
		@yield('body')

		{!! globalSettings('body_scripts') !!}
	</body>
</html>
