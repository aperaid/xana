@extends('layouts.xana.layout')
@section('title')
	Edit Inventory
@stop

@section('content')
{!! Form::model($adjust, [
  'method' => 'post',
  'route' => ['inventory.updateadjustinventory', $adjust->id]
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
            <div class="col-sm-2">
              {!! Form::text('Code', $adjust->Code, array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Barang', 'Barang', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-5">
              {!! Form::text('Barang', $adjust->Barang, array('class' => 'form-control', 'autocomplete' => 'off')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Type', 'Type', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Type', $adjust->Type, ['class' => 'form-control', 'readonly']) !!}
            </div>
            {!! Form::label('JualPrice', 'Jual', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('JualPrice', 'Rp ' . number_format($adjust->JualPrice, 0,',', '.' ), array('id' => 'JualPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
            </div>
            {!! Form::label('Price', 'Sewa', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Price', 'Rp ' . number_format($adjust->Price, 0,',', '.' ), array('id' => 'Price', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 10.000', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Kumbang', 'Kumbang', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('Kumbang', $adjust->Kumbang, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
            {!! Form::label('BulakSereh', 'Bulak Sereh', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('BulakSereh', $adjust->BulakSereh, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Legok', 'Legok', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('Legok', $adjust->Legok, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
            {!! Form::label('CitraGarden', 'Citra Garden', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::number('CitraGarden', $adjust->CitraGarden, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
            </div>
          </div>
        </div>
      </div>
      <!-- box body -->
      <div class="box-footer">
      	<a href="{{route('inventory.adjustinventory')}}"><button type="button" class="btn btn-default pull-left">cancel</button></a>
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
    $("#JualPrice").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop