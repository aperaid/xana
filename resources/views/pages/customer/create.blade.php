@extends('layouts.xana.layout')
@section('title')
	Create Customer
@stop

@section('content')
	{!! Form::open(['route' => 'customer.store']) !!}
<div class="row">
<div class="col-md-12">
<div class="box box-info">
<div class="box-body with-border">
	<div class="form-group">
	{!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-4">
	  {!! Form::text('CCode', null, array('class' => 'form-control', 'id' => 'CCode', 'placeholder' => 'Company Code', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
	  </div>
	</div>
	<div class="form-group">
	{!! Form::label('Nama Perusahaan', 'Nama Perusahaan', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-7">
	  {!! Form::text('Company', null, array('class' => 'form-control', 'id' => 'Company', 'placeholder' => 'Nama Perusahaan', 'onKeyUp' => 'capital()', 'required')) !!}
	  </div>
	  {!! Form::label('NPWP', 'NPWP', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-legal"></i></span>
		  {!! Form::number('NPWP', null, array('class' => 'form-control', 'placeholder' => '12.456.789.0-012.123')) !!}
		</div>
	  </div>
	</div>
	<div class="form-group">
	{!! Form::label('Alamat', 'Alamat', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-4">
	  {!! Form::text('Alamat', null, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota')) !!}
	  </div>
	  {!! Form::label('Kota', 'Kota', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
	  {!! Form::text('Kota', null, array('class' => 'form-control', 'placeholder' => 'Kota')) !!}
	  </div>
	  {!! Form::label('Kodepos', 'Kodepos', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
	  {!! Form::number('Zip', null, array('class' => 'form-control', 'placeholder' => '10203')) !!}
	  </div>
	</div>
	<div class="form-group">
	  {!! Form::label('Telp', 'Telp', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-phone"></i>
		  </div>
		  {!! Form::text('CompPhone', null, array('class' => 'form-control', 'placeholder' => '021-123456')) !!}
		</div>
	  </div>
	  {!! Form::label('Fax', 'Fax', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-fax"></i>
		  </div>
		  {!! Form::text('Fax', null, array('class' => 'form-control', 'placeholder' => '021-123456')) !!}
		</div>
	  </div>
	  {!! Form::label('Email', 'Email', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
		  {!! Form::text('CompEmail', null, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
		</div>
	  </div>
	</div>

	<div class="form-group">
	  {!! Form::label('CP', 'CP', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-10">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-user"></i>
		  </div>
		  {!! Form::text('Customer', null, array('class' => 'form-control', 'placeholder' => 'Nama CP')) !!}
		</div>
	  </div>
	</div>
	<div class="form-group">
	  {!! Form::label('Telp', 'Telp', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-phone"></i>
		  </div>
		  {!! Form::text('CustPhone', null, array('class' => 'form-control', 'placeholder' => '021-123456')) !!}
		</div>
	  </div>
	  {!! Form::label('Email CP', 'Email CP', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
		  {!! Form::text('CustEmail', null, array('class' => 'form-control', 'placeholder' => 'Email CP')) !!}
		</div>
	  </div>
	</div>
	
</div>
<!-- box body -->

<div class="box-footer">
	{!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
	<a href="{{route('customer.index')}}"><button type="button" class="btn btn-default pull-Left">Cancel</button></a>
</div>
<!-- footer -->
{!! Form::close() !!}

</div>
<!-- box -->

</div>
<!-- col -->

</div>
<!-- row -->
@stop

<script>
function capital() {
    var x = document.getElementById("CCode");
    x.value = x.value.toUpperCase();
	var x = document.getElementById("Company");
    x.value = x.value.toUpperCase();
}
</script>