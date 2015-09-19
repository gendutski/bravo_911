<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<meta http-equiv="pragma" content="no-cache" />

	<title>{{config()->get('constants.title')}} - {{ !empty($title)? $title:'The Finance and HR System' }}</title>

	<!-- Bootstrap Core CSS -->
	<link href="{{ config()->get('constants.css_path') }}bootstrap.min.css" rel="stylesheet">

	<!-- MetisMenu CSS -->
	<link href="{{ config()->get('constants.css_path') }}plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

	<!-- Timeline CSS -->
	<link href="{{ config()->get('constants.css_path') }}plugins/timeline.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="{{ config()->get('constants.css_path') }}sb-admin-2.css" rel="stylesheet">

	<!-- datepicker CSS -->
	<link href="{{ config()->get('constants.css_path') }}bootstrap-datepicker3.min.css" rel="stylesheet">

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

	<div id="wrapper">

		<!-- Navigation -->
		<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="{{url('')}}">{{config()->get('constants.title')}}</a>
			</div>
			<!-- /.navbar-header -->

			<ul class="nav navbar-top-links navbar-right">
				<!-- /.dropdown -->
				<li>
					<a href="{{url('user/profile')}}" data-method="get" data-title="User Profile" class="menu-loader"><i class="fa fa-user fa-fw"></i> User Profile</a>
				</li>
				<li>
					<a href="{{url('logout')}}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
				</li>
				<!-- /.dropdown -->
			</ul>
			<!-- /.navbar-top-links -->

			<div class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav navbar-collapse">
					<ul class="nav" id="side-menu">
					@foreach($menu as $row)
						<li>
							<a href="{{$row['api_endpoint']}}" data-method="{{$row['api_method']}}" data-title="{{$row['title']}}" class="menu-loader">
								{{$row['name']}}
								@if(count($row['children']) > 0)
									<span class="fa arrow"></span>
								@endif
							</a>
							@if(count($row['children']) > 0)
								<ul class="nav nav-second-level">
								@foreach($row['children'] as $row2)
								<li>
									<a href="{{$row2['api_endpoint']}}" data-method="{{$row2['api_method']}}" data-title="{{$row2['title']}}" class="menu-loader">
										{{$row2['name']}}
										@if(count($row2['children']) > 0)
											<span class="fa arrow"></span>
										@endif
									</a>
									@if(count($row2['children']) > 0)
										<ul class="nav nav-third-level">
										@foreach($row2['children'] as $row3)
											<li>
												<a href="{{$row3['api_endpoint']}}" data-method="{{$row3['api_method']}}" data-title="{{$row3['title']}}" class="menu-loader">{{$row3['name']}}</a>
											</li>
										@endforeach
										</ul>
									@endif
								</li>
								@endforeach
								</ul>
							@endif
						</li>
					@endforeach
					</ul>
				</div>
				<!-- /.sidebar-collapse -->
			</div>
			<!-- /.navbar-static-side -->
		</nav>

		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header" id="konten-title">User Profile</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			
			
			<div class="row" id="konten-utama">
				<form role="form" method="put" action="{{url('user/profile')}}" role="form" id="form_profile">
					<div class="col-lg-6">
						{!! csrf_field() !!}
						
						<div class="form-group">
							<label>Alamat Email</label>
							<input type="text" class="form-control" name="email" value="{{$user_profile['email']}}" readonly="readonly">
						</div>
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control" name="name" value="{{$user_profile['name']}}">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" class="form-control" name="password" value="" placeholder="Isi untuk mengganti password">
						</div>
						<div class="form-group">
							<label>Konfirmasi Password</label>
							<input type="password" class="form-control" name="cpassword" value="" placeholder="Isi untuk mengganti password">
						</div>
						<button type="submit" class="btn btn-primary" style="margin-right:5px">Submit</button><button type="reset" class="btn btn-warning">Reset</button>
					</div>
				</form>
			</div>
			
			<div class="row" style="margin-bottom:1em"></div>
			
		</div>
		<!-- /#page-wrapper -->

	</div>
	<!-- /#wrapper -->

	<!-- jQuery Version 1.11.0 -->
	<script src="{{ config()->get('constants.js_path') }}jquery-1.11.0.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}bootstrap.min.js"></script>

	<!-- Metis Menu Plugin JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}plugins/metisMenu/metisMenu.min.js"></script>

	<!-- Custom Theme JavaScript -->
	<script src="{{ config()->get('constants.js_path') }}sb-admin-2.js"></script>

	<!-- bootstrap datepicker -->
	<script src="{{ config()->get('constants.js_path') }}bootstrap-datepicker.min.js"></script>
	<script src="{{ config()->get('constants.js_path') }}bootstrap-datepicker.id.min.js"></script>

	<!-- number format -->
	<script src="{{ config()->get('constants.js_path') }}jquery.number.min.js"></script>
	
	<!-- my content loader class -->
	<script src="{{ config()->get('constants.js_path') }}content.loader.js"></script>
	
	<!-- main -->
	<script src="{{ config()->get('constants.js_path') }}main.js"></script>

</body>

</html>
