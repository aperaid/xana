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
	     <div class="box-body with-border">
         <div class="form-horizontal">
          <div class="form-group">
            {!! Form::label('Company Code', 'Company Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
            {!! Form::text('CCode', $customer->CCode, array('class' => 'form-control', 'id' => 'CCode', 'readonly')) !!}
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('Nama Perusahaan', 'Nama Perusahaan', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
            {!! Form::text('Company', $customer->Company, array('class' => 'form-control', 'id' => 'Company', 'placeholder' => 'Nama Perusahaan', 'onKeyUp' => 'capital()', 'required')) !!}
            </div>
            {!! Form::label('NPWP', 'NPWP', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
            <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-legal"></i></span>
            {!! Form::number('NPWP', $customer->NPWP, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota')) !!}
            </div>
            </div>
          </div>

          <div class="form-group">
            {!! Form::label('Alamat', 'Alamat', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-4">
            {!! Form::text('Alamat', $customer->Alamat, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota')) !!}
            </div>
            {!! Form::label('Kota', 'Kota', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Kota', $customer->Kota, array('class' => 'form-control', 'placeholder' => 'Kota')) !!}
            </div>
            {!! Form::label('Kodepos', 'Kodepos', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
            {!! Form::number('Zip', $customer->Zip, array('class' => 'form-control', 'placeholder' => '10203')) !!}
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Telp', 'Telp', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
            <div class="input-group">
            <div class="input-group-addon">
            <i class="fa fa-phone"></i>
            </div>
            {!! Form::text('CompPhone', $customer->CompPhone, array('class' => 'form-control', 'placeholder' => '021-123456')) !!}
            </div>
            </div>
            {!! Form::label('Fax', 'Fax', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
            <div class="input-group">
            <div class="input-group-addon">
            <i class="fa fa-fax"></i>
            </div>
            {!! Form::text('Fax', $customer->Fax, array('class' => 'form-control', 'placeholder' => '021-123456')) !!}
            </div>
            </div>
            {!! Form::label('Email', 'Email', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
            <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
            {!! Form::text('CompEmail', $customer->CompEmail, array('class' => 'form-control', 'placeholder' => 'Email')) !!}
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
