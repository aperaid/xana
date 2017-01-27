@extends('layouts.xana.layout')
@section('title')
	Edit Customer
@stop

@section('content')
{!! Form::model($customer, [
  'method' => 'patch',
  'route' => ['customer.update', $customer->id]
]) !!}
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Company Detail</h3>
      </div>
      <!-- box-header -->
	    <div class="box-body with-border">
        <div class="form-horizontal">
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode', $customer->CCode, array('class' => 'form-control', 'id' => 'CCode', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Name', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', $customer->Company, array('class' => 'form-control', 'id' => 'Company', 'placeholder' => 'PT. COMPANY', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
            {!! Form::label('NPWP', 'NPWP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-legal"></i></span>
              {!! Form::number('NPWP', $customer->NPWP, array('class' => 'form-control', 'placeholder' => '12.456.789.0-012.123', 'autocomplete' => 'off')) !!}
            </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Address', 'Company Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CompAlamat', $customer->CompAlamat, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('City', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('CompKota', $customer->CompKota, array('class' => 'form-control', 'placeholder' => 'Jakarta', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('Zip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('CompZip', $customer->CompZip, array('class' => 'form-control', 'placeholder' => '10203', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company Phone', 'Company Phone', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CompPhone', $customer->CompPhone, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Fax', 'Fax', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-fax"></i>
                </div>
                {!! Form::text('Fax', $customer->Fax, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Email', 'Email', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CompEmail', $customer->CompEmail, array('class' => 'form-control', 'placeholder' => 'company.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Customer 1 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Contact Person', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', $customer->Customer, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
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
                {!! Form::text('CustPhone', $customer->CustPhone, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail', $customer->CustEmail, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Customer 2 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Contact Person', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer2', $customer->Customer2, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
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
                {!! Form::text('CustPhone2', $customer->CustPhone2, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail2', $customer->CustEmail2, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Customer 3 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Contact Person', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer3', $customer->Customer3, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
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
                {!! Form::text('CustPhone3', $customer->CustPhone3, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail3', $customer->CustEmail3, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('customer.show', $customer->id)}}"><button type="button" class="btn btn-default pull-left">cancel</button></a>
      	{!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
      </div>
      <!-- footer -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
{!! Form::close() !!}
@stop

<script>
  function capital() {
    var x = document.getElementById("CCode");
    x.value = x.value.toUpperCase();
    var x = document.getElementById("Company");
    x.value = x.value.toUpperCase();
  }
</script>