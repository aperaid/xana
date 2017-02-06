@extends('layouts.xana.layout')
@section('title')
	Create Reference
@stop

@section('content')
{!! Form::open([
  'route' => 'reference.store'
]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">PO Detail</h3>
			</div>
      <!-- box header -->
      <div class="box-body">
        {!! Form::hidden('id', $reference->maxid+1) !!}
        <div class="form-group">
          {!! Form::label('Reference', 'Reference') !!}
          {!! Form::text('Reference', str_pad($reference->maxid+1, 5, "0", STR_PAD_LEFT).'/'.date("dmy"), array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Transport') !!}
          {!! Form::text('Transport', null, array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000', 'required')) !!}
        </div>
        <div class="form-group">
        @if(Auth::user()->access == 'Admin' || Auth::user()->access == 'POPPN')
          {!! Form::hidden('PPNT', 0) !!}
          {!! Form::checkbox('PPNT', 1, null, ['id' => 'PPNT', 'class' => 'minimal']) !!}
          {!! Form::label('PPNT', 'Transport included in PPN') !!}
        @endif
        </div>
        <div class="form-group">
          {!! Form::label('PCode', 'Project Code') !!}
          {!! Form::text('PCode', null, array('class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'maxlength' => '5', 'style' => 'text-transform: uppercase', 'required')) !!}
          <p class="help-block">Enter the beginning of the Project Code, then pick from the dropdown</p>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
        {!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
        {{ Form::button('Create Customer & Project', array('id' => 'customerproject', 'class' => 'btn btn-success pull-right', 'style' => 'margin-right: 5px')) }}
        <a href="{{route('reference.index')}}"><button type="button" class="btn btn-default pull-Left">Cancel</button></a>
      </div>
      <!-- box footer -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->
{!! Form::close() !!}

<div class="modal fade" id="customerprojectmodal">
  <div class="modal-dialog modal-lg">
  <!-- form start -->
  {!! Form::open(['id' => 'customerprojectform', 'name' => 'customerprojectform']) !!}
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Project Detail</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <!-- box-header -->
      <div class="form-horizontal">
        <div class="box-body">
          {!! Form::hidden('projectid', $project_id, ['id' => 'projectid']) !!}
          <div class="form-group">
            {!! Form::label('PCode2', 'Project Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('PCode2', null, array('class' => 'form-control', 'id' => 'PCode2', 'placeholder' => 'PRO01', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase', 'maxlength' => '6', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Project', 'Project Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Project', null, array('class' => 'form-control', 'id' => 'Project', 'placeholder' => 'Project Name', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase', 'required')) !!}
            </div>
						{!! Form::label('Sales', 'Sales', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              {!! Form::text('Sales', null, array('class' => 'form-control', 'id' => 'Sales', 'placeholder' => 'Sales', 'autocomplete' => 'off', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('ProjAlamat', 'Project Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('ProjAlamat', null, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('ProjKota', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('ProjKota', null, array('class' => 'form-control', 'placeholder' => 'Jakarta', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('ProjZip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('ProjZip', null, array('class' => 'form-control', 'placeholder' => '10203', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CCode', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode', null, array('class' => 'form-control', 'id' => 'CCode', 'placeholder' => 'COM01', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase', 'maxlength' => '5', 'required')) !!}
            </div>
          </div>
        </div>
        <!-- box body -->
      </div>
      <!-- form-horizontal -->
      <!-- /.box-header -->
      <div class="box-header with-border">
        <h3 class="box-title">Customer Details</h3>
      </div>
      <div class="box-body">
        <div class="form-horizontal">
          {!! Form::hidden('customerid', $customer_id, ['id' => 'customerid']) !!}
          <div class="form-group">
            {!! Form::label('CCode2', 'Company Code', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CCode2', null, array('class' => 'form-control', 'id' => 'CCode2', 'placeholder' => 'COM01', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase', 'maxlength' => '5')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Company', 'Company Name', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              {!! Form::text('Company', null, array('class' => 'form-control', 'id' => 'Company', 'placeholder' => 'PT. COMPANY', 'autocomplete' => 'off', 'style' => 'text-transform: uppercase')) !!}
            </div>
            {!! Form::label('NPWP', 'NPWP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-legal"></i></span>
                {!! Form::number('NPWP', null, array('class' => 'form-control', 'placeholder' => '12.456.789.0-012.123', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CompAlamat', 'Company Address', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-4">
              {!! Form::text('CompAlamat', null, array('class' => 'form-control', 'placeholder' => 'Jl. Nama Jalan 1A No.10, Kelurahan, Kecamatan, Kota', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('CompKota', 'City', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::text('CompKota', null, array('class' => 'form-control', 'placeholder' => 'Jakarta', 'autocomplete' => 'off')) !!}
            </div>
            {!! Form::label('CompZip', 'Zip', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-2">
              {!! Form::number('CompZip', null, array('class' => 'form-control', 'placeholder' => '10203', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CompanyPhone', 'Company Phone', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CompPhone', null, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('Fax', 'Fax', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-fax"></i>
                </div>
                {!! Form::text('Fax', null, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('CompEmail', 'Email', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CompEmail', null, array('class' => 'form-control', 'placeholder' => 'company@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <hr>
          <div class="form-group">
            {!! Form::label('Customer', 'Contact Person', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-6">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-user"></i>
                </div>
                {!! Form::text('Customer', null, array('class' => 'form-control', 'placeholder' => 'CP Name', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('CustPhone', 'Phone CP', ['class' => "col-md-2 control-label"]) !!}
            <div class="col-md-2">
              <div class="input-group">
                <div class="input-group-addon">
                  <i class="fa fa-phone"></i>
                </div>
                {!! Form::text('CustPhone', null, array('class' => 'form-control', 'placeholder' => '021-123456', 'autocomplete' => 'off')) !!}
              </div>
            </div>
            {!! Form::label('CustEmail', 'Email CP', ['class' => "col-md-1 control-label"]) !!}
            <div class="col-md-3">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                {!! Form::text('CustEmail', null, array('class' => 'form-control', 'placeholder' => 'person@email.co.id', 'autocomplete' => 'off')) !!}
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="box-footer">
        {{ Form::button('Close', array('class' => 'btn btn-default pull-left', 'data-dismiss' => 'modal')) }}
        {!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
      </div>
      <!-- /.box-footer -->
      <div class="overlay loading" hidden>
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <!-- /.box-loading -->
    </div>
    <!-- /.box -->
  {!! Form::close() !!}
  </div>
</div>
@stop

@section('script')
<script>
  $(function() {
    $('#Tgl').datepicker({
      format: "dd/mm/yyyy",
      todayHighlight: true,
      autoclose: true,
      startDate: '-7d',
      endDate: '+7d'
    }); 
  }); 
</script>
<script>
//When create customer is clicked
$("#customerproject").click(function(){
  //Toggle the modal
  $('#customerprojectmodal').modal('toggle');
});

//When customer form is submitted
$("#customerprojectform").submit(function(event){
  $(".loading").show();
  $.post( "customerproject",{ "_token": "{{ csrf_token() }}", projectid: $("#projectid").val(), PCode: $("#PCode2").val(), Project: $("#Project").val(), Sales: $("#Sales").val(), ProjAlamat: $("#ProjAlamat").val(), ProjZip: $("#ProjZip").val(), ProjKota: $("#ProjKota").val(), CCode2: $("#CCode2").val(),customerid: $("#customerid").val(), CCode: $("#CCode").val(), Company: $("#Company").val(), Customer: $("#Customer").val(), CompAlamat:$("#CompAlamat").val(), CompZip: $("#CompZip").val(), CompKota: $("#CompKota").val(), CompPhone: $("#CompPhone").val(), CompEmail: $("#CompEmail").val(), CustPhone: $("#CustPhone").val(), CustEmail: $("#CustEmail").val(), Fax: $("#Fax").val(), NPWP: $("#NPWP").val() }, function( data ) {})
  .done(function(data){
    location.reload();
    $('#customerprojectmodal').modal('toggle');
    $(".loading").hide();
  })
  .fail(function(data) {
    alert("Failed");
  });
  event.preventDefault();
});

$(function() {
	var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompleteccode.php");?>;
	$( "#CCode" ).autocomplete({
		source: availableTags,
		autoFocus: true,
		appendTo: "#customerprojectform"
	});
});

$(function() {
	var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompletepcode.php");?>;
	$( "#PCode" ).autocomplete({
		source: availableTags,
		autoFocus: true
	});
});

$(document).ready(function(){
	//Mask Transport
	$("#Transport").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	//iCheck
	$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
		checkboxClass: 'icheckbox_flat-green',
		increaseArea: '20%' // optional
	});
});
</script>
@stop