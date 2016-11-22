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
      <div class="box-header with-border">
        <h3 class="box-title">Customer Detail</h3>
      </div>
      <!-- box-header -->
      <div class="box-body with-border">
        <div class="form-horizontal">
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
               {!! Form::text('CCode', $customer->CCode, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            <!-- NAMA PERUSAHAAN -->
            {!! Form::label('Company Name', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', $customer->Company, array('class' => 'form-control', 'readonly')) !!}
            </div>
            <!-- NPWP -->
            {!! Form::label('NPWP', 'NPWP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-legal"></i></span>
                {!! Form::number('NPWP', $customer->NPWP, array('class' => 'form-control', 'readonly', 'col-md-2')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            <!-- Alamat -->
            {!! Form::label('Company Address', 'Company Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CompAlamat', $customer->CompAlamat, array('class' => 'form-control', 'readonly')) !!}
            </div>
            <!-- Kota -->
            {!! Form::label('City', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('CompKota', $customer->CompKota, array('class' => 'form-control', 'readonly')) !!}
            </div>
            <!-- Kodepos -->
            {!! Form::label('Zip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('CompZip', $customer->CompZip, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Phone', 'Company Phone', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CompPhone', $customer->CompPhone, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Fax', 'Fax', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-fax"></i>
                </div>
                {!! Form::text('Fax', $customer->Fax, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Email', 'Email', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CompEmail', $customer->CompEmail, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Contact Person', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', $customer->Customer, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Phone CP', 'Phone CP', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CustPhone', $customer->CustPhone, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail', $customer->CustEmail, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('customer.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
      	<button type="submit" style="margin-right: 5px;" class="btn btn-danger pull-left" onclick="return confirm('Delete Customer?')">Delete</button>
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