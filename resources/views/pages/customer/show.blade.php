@extends('layouts.xana.layout')
@section('title')
	View Customer
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Company Detail</h3>
      </div>
      <!-- box-header -->
      <div class="box-body with-border">
        <div class="form-horizontal">
					<input type="hidden" name="id" id="id" value="{{$customer->id}}">
          <div class="form-group">
            {!! Form::label('Customer Code', 'Customer Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
               <input type="text" id="CCode" value="{{$customer->CCode}}" class="form-control" readonly>
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
                {!! Form::text('Customer2', $customer->Customer2, array('class' => 'form-control', 'readonly')) !!}
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
                {!! Form::text('CustPhone2', $customer->CustPhone2, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail2', $customer->CustEmail2, array('class' => 'form-control', 'readonly')) !!}
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
                {!! Form::text('Customer3', $customer->Customer3, array('class' => 'form-control', 'readonly')) !!}
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
                {!! Form::text('CustPhone3', $customer->CustPhone3, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            {!! Form::label('Email CP', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail3', $customer->CustEmail3, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('customer.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
      	<button type="button" style="margin-right: 5px;" id="delete" @if ( $checkcust == 1 )	class="btn btn-default pull-right" disabled	@else	class="btn btn-danger pull-right" @endif>Delete</button>
      	<a href="{{route('customer.edit', $customer->id)}}"><button type="button" class="btn btn-info pull-right">Edit</button></a>
      </div>
      <!-- box footer -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
@stop

<div class="modal fade" id="deletemodal">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <!-- form start -->
      <form id="deleteform" name="deleteform" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete</h4>
        </div>
        <div class="modal-body">
          <label class="text-default" data-toggle="modal"><h4> Are you sure you want to delete this Customer? (Delete Permanently)</h4></label>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger pull-right">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('script')
<script>
$(document).ready(function(){
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});

//When delete button is clicked
$("#delete").click(function(){
  //Toggle the modal
  $('#deletemodal').modal('toggle');
});

//When delete form is submitted
$("#deleteform").submit(function(event){
  $.post("../delete", { "_token": "{{ csrf_token() }}", id: $("#id").val(), CCode: $("#CCode").val() }, function(data){})
  .done(function(data){
		window.location.replace("/customer");
  });
});
</script>
@stop