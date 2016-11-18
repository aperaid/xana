@extends('layouts.xana.layout')
@section('title')
	Edit PO
@stop

@section('content')
	{!! Form::model($po, [
	'method' => 'patch',
	'route' => ['po.update', $po->id]
	]) !!}
	
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('po.show', $po -> id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        <button type="submit" class="btn btn-success pull-right">Update</button>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

<!-- CONTENT ROW -->
<div class="row">
  <!-- PO INFO BOX -->
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Detail</h3>
      </div>
      <div class="box-body">
        {!! Form::hidden('poid', $po->id) !!}
        {!! Form::hidden('Reference', $transaksi -> Reference) !!}
        <div class="form-group">
          {!! Form::label('Nomor PO', 'Nomor PO') !!}
          {!! Form::text('POCode', $po -> POCode, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          {!! Form::text('Tgl', $po -> Tgl, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Date', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Transport') !!}
          {!! Form::text('Transport', 'Rp ' . number_format( $po -> Transport, 0,',', '.' ), array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Transport Fee', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Catatan', 'Catatan') !!}
          {!! Form::textarea('Catatan', $po -> Catatan, array('id' => 'Catatan', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5', 'required')) !!}
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
  <!-- POITEM BOX -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th><a id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang</th>
            <th>J/S</th>
            <th>Quantity</th>
            <th>Price</th>
          </thead>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
    <!-- /.col -->
</div>
<!-- /.row -->

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
    $(document).ready(function(){
		var max_fields      = 10; //maximum input boxes allowed
		
    var x = 0; //initial text box count
		var y = {{ $transaksi -> id }};
		var z = y;
    
		@foreach($transaksis as $transaksi)
				$("#customFields").append('<tr><td><a class="remCF glyphicon glyphicon-remove"></a></td>{!! Form::hidden('transaksiid[]', $transaksi->id) !!}{!! Form::hidden('Purchase[]', $transaksi->Purchase) !!}<td>{!! Form::text('Barang[]', $transaksi->Barang, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], $transaksi->JS, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Quantity[]', $transaksi->Quantity, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::number('Amount[]', $transaksi->Amount, ['id' => 'Amount', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
		@endforeach
		
		$("#addCF").click(function(){
			if(x < max_fields){ //max input box allowed
				x++; //text box count increment
				z++;
        var id = x + y;
				$("#customFields").append('<tr><td><a href="javascript:void(0);" class="remCF glyphicon glyphicon-remove"></a></td><input type="hidden" name="transaksiid[]" value="'+ id +'"><input type="hidden" name="Purchase[]" value="'+ z +'"><td>{!! Form::text('Barang[]', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Main Frame', 'required']) !!}</td><td>{!! Form::select('JS[]', ['Jual' => 'Jual', 'Sewa' => 'Sewa'], null, ['class' => 'form-control']) !!}</td><td>{!! Form::number('Quantity[]', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '100', 'required']) !!}</td><td>{!! Form::number('Amount[]', null, ['id' => 'Amount', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp 100.000', 'required']) !!}</td></tr>');
			}
		});
		
		$("#customFields").on('click','.remCF',function(){
			$(this).parent().parent().remove();
			x--;
		});	
	});
</script>
<script>
  $(document).ready(function(){
		//Mask Transport
		$("#Transport").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
		//Mask Price
		$("#Discount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
    $("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop