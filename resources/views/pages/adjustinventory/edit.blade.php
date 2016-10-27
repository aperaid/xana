@extends('layouts.xana.layout')
@section('title')
	Edit Inventory
@stop

@section('content')
{!! Form::model($adjust, [
  'method' => 'patch',
  'route' => ['adjustinventory.update', $adjust->id]
]) !!}
<div class="row">
  <div class="col-md-12">
    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">Inventory Detail</h3>
      </div>
	     <div class="box-body with-border">
         <div class="form-horizontal">
          <div class="form-group">
            {!! Form::label('Code', 'Code', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Code', $adjust->Code, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Barang', 'Barang', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Barang', $adjust->Barang, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Price', 'Price', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Price', 'Rp ' . number_format($adjust->Price, 0,',', '.' ), array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000', 'required')) !!}
            </div>
            {!! Form::label('Jumlah', 'Jumlah', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::number('Jumlah', $adjust->Jumlah, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Type', 'Type', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Type', $adjust->Type, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Baru', 'required')) !!}
            </div>
            {!! Form::label('Warehouse', 'Warehouse', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('Warehouse', $adjust->Warehouse, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Gudang 1', 'required')) !!}
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('adjustinventory.index')}}"><button type="button" class="btn btn-default pull-left">cancel</button></a>
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
  $(document).ready(function(){
		//Mask Price
		$("#Price").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop