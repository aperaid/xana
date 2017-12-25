@extends('layouts.xana.layout')
@section('title')
	Edit Supplier
@stop

@section('content')
{!! Form::open([
  'route' => 'supplier.update'
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
					<input type="hidden" name="id" id="id" value="{{$supplier->id}}">
          <div class="form-group">
            {!! Form::label('SCode', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('SCode', $supplier->SCode, array('class' => 'form-control', 'id' => 'SCode', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', $supplier->Company, array('class' => 'form-control', 'id' => 'Company', 'placeholder' => 'PT. COMPANY', 'autocomplete' => 'off', 'onKeyUp' => 'capital()')) !!}
            </div>
            {!! Form::label('NPWP', 'NPWP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-legal"></i></span>
              {!! Form::number('NPWP', $supplier->NPWP, array('class' => 'form-control', 'placeholder' => '12.456.789.0-012.123', 'autocomplete' => 'off')) !!}
            </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CompAlamat', 'Company Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CompAlamat', $supplier->CompAlamat, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('CompKota', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('CompKota', $supplier->CompKota, array('class' => 'form-control', 'placeholder' => 'Jakarta', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('CompZip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('CompZip', $supplier->CompZip, array('class' => 'form-control', 'placeholder' => '10203', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CompPhone', 'Company Phone', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CompPhone', $supplier->CompPhone, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Fax', 'Fax', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-fax"></i>
                </div>
                {!! Form::text('Fax', $supplier->Fax, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('CompEmail', 'Email', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CompEmail', $supplier->CompEmail, array('class' => 'form-control', 'placeholder' => 'company.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Supplier 1 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Supplier', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Supplier', $supplier->Supplier, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('SupPhone', 'Phone CP', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('SupPhone', $supplier->SupPhone, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('SupEmail', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('SupEmail', $supplier->SupEmail, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Supplier 2 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Supplier2', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Supplier2', $supplier->Supplier2, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('SupPhone2', 'Phone CP', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('SupPhone2', $supplier->SupPhone2, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('SupEmail2', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('SupEmail2', $supplier->SupEmail2, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="box-header">
            <h3 class="box-title">Supplier 3 Detail</h3>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Supplier3', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Supplier3', $supplier->Supplier3, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('SupPhone3', 'Phone CP', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('SupPhone3', $supplier->SupPhone3, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('SupEmail3', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('SupEmail3', $supplier->SupEmail3, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('supplier.show', $supplier->id)}}"><button type="button" class="btn btn-default pull-left">cancel</button></a>
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

@section('script')
<script>
function capital() {
	var x = document.getElementById("SCode");
	x.value = x.value.toUpperCase();
	var x = document.getElementById("Company");
	x.value = x.value.toUpperCase();
}
	
$(document).ready(function(){
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});
</script>
@stop