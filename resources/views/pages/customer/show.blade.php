@extends('layouts.xana.layout')
@section('title')
	View Customer
@stop

@section('content')
	{!! Form::open([
	'method' => 'delete',
	'route' => ['customer.destroy', $customer->id]
	]) !!}
	
<div class="row">
<div class="col-md-12">
<div class="box box-info">
	<div class="box-body with-border">
	<div class="form-group">
	{!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-4">
	  {!! Form::text('CCode', $customer->CCode, array('class' => 'form-control', 'readonly')) !!}
	  </div>
	</div>
	<div class="form-group">
	{!! Form::label('Nama Perusahaan', 'Nama Perusahaan', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-7">
	  {!! Form::text('Company', $customer->Company, array('class' => 'form-control', 'readonly')) !!}
	  </div>
	  {!! Form::label('NPWP', 'NPWP', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-legal"></i></span>
		  {!! Form::number('NPWP', $customer->NPWP, array('class' => 'form-control', 'readonly')) !!}
		</div>
	  </div>
	</div>
	<div class="form-group">
	{!! Form::label('Alamat', 'Alamat', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-4">
	  {!! Form::text('Alamat', $customer->Alamat, array('class' => 'form-control', 'readonly')) !!}
	  </div>
	  {!! Form::label('Kota', 'Kota', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
	  {!! Form::text('Kota', $customer->Kota, array('class' => 'form-control', 'readonly')) !!}
	  </div>
	  {!! Form::label('Kodepos', 'Kodepos', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
	  {!! Form::number('Zip', $customer->Zip, array('class' => 'form-control', 'readonly')) !!}
	  </div>
	</div>
	<div class="form-group">
	  {!! Form::label('Telp', 'Telp', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-phone"></i>
		  </div>
		  {!! Form::text('CompPhone', $customer->CompPhone, array('class' => 'form-control', 'readonly')) !!}
		</div>
	  </div>
	  {!! Form::label('Fax', 'Fax', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <div class="input-group-addon">
			<i class="fa fa-fax"></i>
		  </div>
		  {!! Form::text('Fax', $customer->Fax, array('class' => 'form-control', 'readonly')) !!}
		</div>
	  </div>
	  {!! Form::label('Email', 'Email', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
		  {!! Form::text('CompEmail', $customer->CompEmail, array('class' => 'form-control', 'readonly')) !!}
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
		  {!! Form::text('Customer', $customer->Customer, array('class' => 'form-control', 'readonly')) !!}
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
		  {!! Form::text('CustPhone', $customer->CustPhone, array('class' => 'form-control', 'readonly')) !!}
		</div>
	  </div>
	  {!! Form::label('Email CP', 'Email CP', ['class' => "col-sm-2 control-label"]) !!}
	  <div class="col-sm-2">
		<div class="input-group">
		  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
		  {!! Form::text('CustEmail', $customer->CustEmail, array('class' => 'form-control', 'readonly')) !!}
		</div>
	  </div>
	</div>
	
</div>
<!-- box body -->

<div class="box-footer">
	<a href="{{route('customer.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
	{!! Form::submit('Delete',  array('class' => 'btn btn-danger pull-left')) !!}
	<a href="{{route('customer.edit', $customer->id)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
</div>
<!-- footer -->
{!! Form::close() !!}

</div>
<!-- col -->

</div>
<!-- row -->

@stop