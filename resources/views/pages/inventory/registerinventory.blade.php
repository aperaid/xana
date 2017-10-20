@extends('layouts.xana.layout')
@section('title')
	Create Inventory
@stop

@section('content')
{!! Form::open(['route' => 'inventory.storeRegisterinventory']) !!}
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Inventory Detail</h3>
      </div>
      <div class="box-body with-border">
        <div class="form-horizontal">
          {!! Form::hidden('id', $register->id+1) !!}
          <div class="form-group">
            {!! Form::label('Code', 'Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Code', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'MF190', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
            {!! Form::label('Barang', 'Barang', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-5">
              {!! Form::text('Barang', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Main Frame 190',  'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('JualPrice', 'Jual Price', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('JualPrice', null, array('id' => 'JualPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
            </div>
            {!! Form::label('Price', 'Sewa Price', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Price', null, array('id' => 'Price', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 10.000', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Kumbang', 'Kumbang', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('Kumbang', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
            {!! Form::label('BulakSereh', 'Bulak Sereh', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('BulakSereh', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Legok', 'Legok', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('Legok', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
            {!! Form::label('CitraGarden', 'Citra Garden', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('CitraGarden', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	{!! Form::submit('Create',  array('class' => 'btn btn-info pull-right')) !!}
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
	var x = document.getElementById("Code");
	x.value = x.value.toUpperCase();
	var x = document.getElementById("Barang");
	x.value = x.value.toUpperCase();
}
	
$(document).ready(function(){
	//Mask Price
	$("#Price").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	$("#JualPrice").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
});
	
var availableTags = 
<?php 
	if(env('APP_VM')==0)
		$path = "C:/wamp64/www";
	else if(env('APP_VM')==1)
		$path = "/var/www/html";
	include ($path."/xana/app/Includes/autocompletebarang.php");
?>;

$( "#Barang" ).autocomplete({
	source: availableTags,
	autoFocus: true
});
</script>
@stop