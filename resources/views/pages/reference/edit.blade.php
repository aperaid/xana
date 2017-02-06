@extends('layouts.xana.layout')
@section('title')
	Edit Reference
@stop

@section('content')
{!! Form::model($reference, [
  'method' => 'patch',
  'route' => ['reference.update', $reference->id]
]) !!}
<div class="row">
  <div class="col-md-6 col-md-offset-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Detail</h3>
      </div>
      <!-- box-header -->
      <div class="box-body">
        {!! Form::hidden('id', $reference->id) !!}
        <div class="form-group">
          {!! Form::label('Reference', 'Reference') !!}
          {!! Form::text('Reference', $reference->Reference, ['class' => 'form-control', 'readonly']) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Date', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $reference->Tgl, ['class' => 'form-control', 'id' => 'Tgl', 'autocomplete' => 'off', 'required']) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Transport') !!}
          {!! Form::text('Transport', 'Rp ' . number_format( $reference -> Transport, 0,',', '.' ), array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Transport Fee', 'required')) !!}
        </div>
        <div class="form-group">
        @if(Auth::user()->access == 'Admin' || Auth::user()->access == 'POPPN')
          {!! Form::hidden('PPNT', 0) !!}
          {!! Form::checkbox('PPNT', 1, $reference->PPNT, ['id' => 'PPNT', 'class' => 'minimal']) !!}
          {!! Form::label('PPNT', 'Transport included in PPN') !!}
        @endif
        </div>
        <div class="form-group">
          {!! Form::label('Project Code', 'Project Code') !!}
          {!! Form::text('PCode', $reference->PCode, ['class' => 'form-control', 'id' => 'PCode', 'placeholder' => 'ABC01', 'autocomplete' => 'off', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required']) !!}
          <p class="help-block">Enter the beginning of the Project Code, then pick from the dropdown</p>
        </div>
      </div>
      <!-- box-body -->
      <div class="box-footer">
        <a href="{{route('reference.show', $reference -> id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a> 
        <button name="bt_editpocustomer_submit" type="submit" id="bt_editpocustomer_submit" class="btn btn-primary pull-right">Update</button>
      </div>
      <!-- box-footer -->
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
$('#Tgl').datepicker({
	format: "dd/mm/yyyy",
	todayHighlight: true,
	autoclose: true,
	startDate: '-7d',
	endDate: '+7d'
}); 

$(function() {
	var availableTags = <?php include ("C:/wamp64/www/xana/app/Includes/autocompleteccode.php");?>;
	$( "#tx_insertproject_CCode" ).autocomplete({
		source: availableTags,
	autoFocus: true
	});
});

function capital() {
	var x = document.getElementById("PCode");
	x.value = x.value.toUpperCase();
}

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