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
            <div class="col-sm-3">
              {!! Form::text('Code', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'MF190', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
            </div>
            {!! Form::label('Barang', 'Barang', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Barang', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Main Frame 190', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Price', 'Price', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Price', null, array('id' => 'Price', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000', 'required')) !!}
            </div>
            {!! Form::label('Jumlah', 'Jumlah', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::number('Jumlah', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Type', 'Type', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Type', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Baru', 'required')) !!}
            </div>
            {!! Form::label('Warehouse', 'Warehouse', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Warehouse', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Gudang 1', 'required')) !!}
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
  }
</script>
<script>
  $(document).ready(function(){
		//Mask Price
		$("#Price").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop