@extends('layouts.xana.layout')
@section('title')
	Create PO
@stop

@section('content')
	{!! Form::open([
  'route' => 'po.store'
  ]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('reference.show', $id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
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
        {!! Form::hidden('id', $id) !!}
        {!! Form::hidden('poid', $po->id+1) !!}
        {!! Form::hidden('Reference', $reference -> Reference) !!}
        <div class="form-group">
          {!! Form::label('Nomor PO', 'Nomor PO') !!}
          {!! Form::text('POCode', null, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Input PO Number', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Transport') !!}
          {!! Form::text('Transport', null, array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000', 'required')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Catatan', 'Catatan') !!}
          {!! Form::textarea('Catatan', null, array('id' => 'Catatan', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5', 'required')) !!}
        </div>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
  <!-- POITEM BOX -->
  <div class="col-md-9">
    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Input PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
            <th><a href="javascript:void(0);" id="addCF" class=" glyphicon glyphicon-plus"></a></th>
            <th>Barang</th>
            <th>J/S</th>
            <th>Quantity</th>
            <th>Price</th>
          </thead>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</div>
<!-- row -->

{!! Form::close() !!}
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
    $("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	});
</script>
@stop