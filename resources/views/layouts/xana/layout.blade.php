<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Xana ERP | @yield('title')</title>
    <!-- css include -->
    @include('layouts.xana.css')
  </head>

  <body class="hold-transition skin-blue fixed sidebar-mini">
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
          {{ $page_title }}
          <small>{{ $page_description or null }}</small>
          @yield('button')
          </h1>
          <ol class="breadcrumb">
          <li><a href="{{url('home')}}"><i class="fa fa-dashboard"></i>Home</a></li>
          <li><a href="{{url($url)}}">{{ ucfirst($url) }}</a></li>
          <li class="active">{{ $page_description }}</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-check"></i> Success</h4>
              {{Session::get('message')}}
            </div>
          @endif
          @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              {{Session::get('error')}}
            </div>
          @endif
          <div id="globalmessage">
          </div>
          
          @if (count($errors) > 0)
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Error!</h4>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </div>
          @endif
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

@yield('script')
