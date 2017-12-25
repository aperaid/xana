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
						@if(env('APP_TYPE')=='Sewa')
							{!! Form::label('Code', 'Code', ['class' => "col-sm-3 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('Code', $adjust->Code, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'MF190', 'onKeyUp' => 'capital()', 'maxlength' => '5', 'required')) !!}
							</div>
							{!! Form::label('Type', 'Type', ['class' => "col-sm-1 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('Type', $adjust->Type, ['class' => 'form-control', 'readonly']) !!}
							</div>
						@else
							{!! Form::label('Code', 'Code', ['class' => "col-sm-3 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('Code', $adjust->Code, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'MF190', 'onKeyUp' => 'capital()', 'required')) !!}
							</div>
							{!! Form::label('Type', 'Kategori', ['class' => "col-sm-1 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('Type', $adjust->Type, ['class' => 'form-control', 'autocomplete' => 'off', 'required']) !!}
							</div>
						@endif
          </div>
          <div class="form-group">
            {!! Form::label('Barang', 'Barang', ['class' => "col-sm-3 control-label"]) !!}
            <div class="col-sm-5">
              {!! Form::text('Barang', $adjust->Barang, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Main Frame 190',  'onKeyUp' => 'capital()', 'required')) !!}
            </div>
          </div>
          <div class="form-group">
						@if(env('APP_TYPE')=='Sewa')
							{!! Form::label('BeliPrice', 'Beli Price', ['class' => "col-sm-2 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('BeliPrice', 'Rp ' . number_format($adjust->BeliPrice, 0,',', '.' ), array('id' => 'BeliPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
							</div>
							{!! Form::label('JualPrice', 'Jual Price', ['class' => "col-sm-1 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('JualPrice', 'Rp ' . number_format($adjust->JualPrice, 0,',', '.' ), array('id' => 'JualPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
							</div>
							{!! Form::label('Price', 'Sewa Price', ['class' => "col-sm-1 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('Price', 'Rp ' . number_format($adjust->Price, 0,',', '.' ), array('id' => 'Price', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 10.000', 'required')) !!}
							</div>
						@else
							{!! Form::label('BeliPrice', 'Beli Price', ['class' => "col-sm-3 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('BeliPrice', 'Rp ' . number_format($adjust->BeliPrice, 0,',', '.' ), array('id' => 'BeliPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
							</div>
							{!! Form::label('JualPrice', 'Jual Price', ['class' => "col-sm-1 control-label"]) !!}
							<div class="col-sm-2">
								{!! Form::text('JualPrice', 'Rp ' . number_format($adjust->JualPrice, 0,',', '.' ), array('id' => 'JualPrice', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 1.000.000', 'required')) !!}
							</div>
						@endif
          </div>
					@if(env('APP_TYPE')=='Sewa')
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
					@else
						<div class="form-group">
							{!! Form::label('Warehouse', 'Quantity', ['class' => "col-sm-3 control-label"]) !!}
							<div class="col-sm-5">
								{!! Form::number('Warehouse', $adjust->Warehouse, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required')) !!}
							</div>
						</div>
					@endif
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
function capital() {
	var x = document.getElementById("Code");
	x.value = x.value.toUpperCase();
	var x = document.getElementById("Barang");
	x.value = x.value.toUpperCase();
}

$(document).ready(function(){
	//Mask Price
	$("#BeliPrice").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	$("#JualPrice").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	$("#Price").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
});

//autocomplete on ICode
$("#Code").autocomplete({
	autoFocus: true,
	source: function( request, response ) {
		$.post("/dropdown/icodelist/" + request.term, { '_token':'{{ csrf_token() }}' }, function(data){})
		.done(function(data){
			response($.map(data, function (value, key) {
				return {
						label: value.label
				};
			}));
		})
		.fail(function(){})
	}
});
	
//autocomplete on Barang
$("#Barang").autocomplete({
	autoFocus: true,
	source: function( request, response ) {
		$.post("/dropdown/inventorylist/" + request.term, { '_token':'{{ csrf_token() }}' }, function(data){})
		.done(function(data){
			response($.map(data, function (value, key) {
				return {
						label: value.label
				};
			}));
		})
		.fail(function(){})
	}
});
</script>
@stop