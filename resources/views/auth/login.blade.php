<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>{{config()->get('constants.title')}} - {{ !empty($title)? $title:'The Finance and HR System' }}</title>

	<!-- Bootstrap Core CSS -->
	<link href="{{ config()->get('constants.css_path') }}bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="{{ config()->get('constants.css_path') }}plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="{{ config()->get('constants.css_path') }}sb-admin-2.css" rel="stylesheet">

	<!-- Custom Fonts -->
	<link href="{{ config()->get('constants.fonts_awesome') }}css/font-awesome.min.css" rel="stylesheet" type="text/css">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>

<body>
	
	<div class="container">
		@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		
		<div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="login-panel panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Please Sign In - The Finance and HR System</h3>
					</div>
					<div class="panel-body">
						
						<div class="row" style="text-align:center;padding:1em">
							<img src="{{config()->get('constants.images_path')}}logo.png" alt="" title="Bravo 911 - The Finance and HR System" />
						</div>
						
						<form role="form" method="post" action="{{url('login')}}">
							{!! csrf_field() !!}
							<fieldset>
								<div class="form-group">
									<input class="form-control" placeholder="E-mail" name="email" type="email" autofocus value="{{ old('email') }}">
								</div>
								<div class="form-group">
									<input class="form-control" placeholder="Password" name="password" type="password" value="">
								</div>
								<div class="checkbox">
									<label>
										<input name="remember" type="checkbox" value="Remember Me">Remember Me
									</label>
								</div>
								<!-- Change this to a button or input when using this as a form -->
								<button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- jQuery Version 1.11.0 -->
	<script src="{{ config()->get('constants.js_path') }}jquery-1.11.0.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}bootstrap.min.js"></script>

	<!-- Metis Menu Plugin JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}plugins/metisMenu/metisMenu.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}sb-admin-2.js"></script>

</body>

</html>
