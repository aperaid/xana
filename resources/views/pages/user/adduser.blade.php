@extends('layouts.xana.layout')
@section('title')
	Add User
@stop

@section('content')
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="/dashboard"><b>Xana</b>ERP</a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Register a new User</p>

    <form name="addform" action="" method="post">
      {{ csrf_field() }}
      <div class="form-group has-feedback">
        <input id="name" type="name" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full name" autocomplete="off">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}" placeholder="Username" autocomplete="off">
        <span class="fa fa-user-plus form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password" type="password" class="form-control" name="password" placeholder="Password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" autocomplte="off">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <div class="form-group">
          <select id="access" class="form-control" name="access" style="width: 100%;">
            <option value="" disabled selected>Choose Access</option>
            <option value="Administrator">Administrator</option>
						<option value="PPNAdmin">PPN Admin</option>
						<option value="NonPPNAdmin">Non PPN Admin</option>
						<!--<option value="Purchasing">Purchasing</option>
						<option value="SuperPurchasing">SuperPurchasing</option>
						<option value="StorageManager">StorageManager</option>
						<option value="SuperStorageManager">SuperStorageManager</option>-->
          </select>
        </div>
        <span class="fa fa-user-secret form-control-feedback"></span>
      </div>

          <button type="submit" class="btn btn-primary btn-block btn-flat pull-right">Register</button>
    </form>

    <a href="/login" class="text-center">Existing Account</a>
  </div>
  <!-- /.form-box -->
</div>
<!-- /.register-box -->

</body>
@endsection
