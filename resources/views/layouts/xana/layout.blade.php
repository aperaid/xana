<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Xana ERP | @yield('title')</title>
  <!-- css include -->
  @include('layouts.xana.css')
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<!--main header -->
	@include('layouts.xana.mainheader')
	
	<!-- Sidebar Menu -->
	@include('layouts.xana.menu')
	
	<!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
		  <h1>
			{{ $page_title or "Page Title" }}
			<small>{{ $page_description or null }}</small>
			@yield('button')
		  </h1>
		  <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
			<li class="active">{{ $page_description }}</li>
		  </ol>
		</section>

		<!-- Main content -->
		<section class="content">

		  <!-- Your Page Content Here -->
		  @yield('content')
		</section>
		<!-- /.content -->
	  </div>
	  <!-- /.content-wrapper -->
	<!-- Main Footer -->
	  @include('layouts.xana.footer')

	</div>
	<!-- jsinclude -->
	@include('layouts.xana.js')
	</body>
</html>

<script>
$(function (){
	$("#datatables").DataTable();
})
</script>