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
      <div class="form-horizontal">
        <div class="box-body">

        <div class="form-group">
          {!! Form::label('Company Code', 'Company Code', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-4">
      	     {!! Form::text('CCode', $customer->CCode, array('class' => 'form-control', 'readonly')) !!}
      	  </div>
      	</div>

        <div class="form-group">
          <!-- NAMA PERUSAHAAN -->
          {!! Form::label('Nama Perusahaan', 'Nama Perusahaan', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-4">
            {!! Form::text('Company', $customer->Company, array('class' => 'form-control', 'readonly')) !!}
          </div>
          <!-- NPWP -->
          {!! Form::label('NPWP', 'NPWP', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-legal"></i></span>
              {!! Form::number('NPWP', $customer->NPWP, array('class' => 'form-control', 'readonly', 'col-md-2')) !!}
            </div>
          </div>
        </div>

        <div class="form-group">
          <!-- Alamat -->
          {!! Form::label('Alamat', 'Alamat', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-4">
            {!! Form::text('Alamat', $customer->Alamat, array('class' => 'form-control', 'readonly')) !!}
          </div>
          <!-- Kota -->
          {!! Form::label('Kota', 'Kota', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
            {!! Form::text('Kota', $customer->Kota, array('class' => 'form-control', 'readonly')) !!}
          </div>
          <!-- Kodepos -->
          {!! Form::label('Kodepos', 'Kodepos', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
            {!! Form::number('Zip', $customer->Zip, array('class' => 'form-control', 'readonly')) !!}
          </div>
        </div>
        <hr>
        <div class="form-group">
          {!! Form::label('Telp', 'Telp', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
          	<div class="input-group">
          	  <div class="input-group-addon">
          		    <i class="fa fa-phone"></i>
          	  </div>
          	  {!! Form::text('CompPhone', $customer->CompPhone, array('class' => 'form-control', 'readonly')) !!}
          	</div>
          </div>
          {!! Form::label('Fax', 'Fax', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
          	<div class="input-group">
          	  <div class="input-group-addon">
          		    <i class="fa fa-fax"></i>
          	  </div>
          	  {!! Form::text('Fax', $customer->Fax, array('class' => 'form-control', 'readonly')) !!}
          	</div>
          </div>
          {!! Form::label('Email', 'Email', ['class' => "col-md-1 control-label"]) !!}
          <div class="col-md-2">
          	<div class="input-group">
          	  <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
          	  {!! Form::text('CompEmail', $customer->CompEmail, array('class' => 'form-control', 'readonly')) !!}
          	</div>
          </div>
        </div>

        </div>
        <!-- box body -->
      </div>
      <!-- form-horizontal -->

      <div class="box-footer">
      	<a href="{{route('customer.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
      	{!! Form::submit('Delete',  array('class' => 'btn btn-danger pull-left')) !!}
      	<a href="{{route('customer.edit', $customer->id)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
      </div>
      <!-- box footer -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->

{!! Form::close() !!}
@stop
