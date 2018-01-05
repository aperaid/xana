@extends('layouts.xana.layout')
@section('title')
	Edit Penerimaan
@stop

@section('content')
	{!! Form::model($penerimaan, [
	'method' => 'patch',
	'route' => ['penerimaan.update', $penerimaan->id]
	]) !!}
	
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('penerimaan.show', $penerimaan->id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        <button type="submit" class="btn btn-success pull-right">Update</button>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->

<div class="row">
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Penerimaan Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('TerimaCode', 'Terima Code') !!}
          {!! Form::text('TerimaCode', $penerimaan->TerimaCode, array('id' => 'TerimaCode', 'class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $penerimaan->Date, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Biaya Transport') !!}
          {!! Form::text('Transport', 'Rp '. number_format( $penerimaan -> Transport, 0,',', '.' ), array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('PesanCode', 'Pesan Code') !!}
          {!! Form::text('PesanCode', $penerimaan->PesanCode, array('class' => 'form-control', 'id' => 'PesanCode', 'readonly')) !!}
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-hover table-bordered" id="customFields">
          <thead>
						<tr>
							<th hidden>Id</th>
							<th hidden>Quantity</th>
							<th>Barang</th>
							<th>ICode</th>
							<th width="10%">QTerima</th>
							@if(env('APP_TYPE')=='Jual')
								<th width="30%">Kategori</th>
							@endif
						</tr>
					</thead>
					<tbody>
						@foreach($pemesananlists as $pemesananlist)
							<tr class='tr_input'>
								<td hidden><input type='text' name='Id[]' id='Id' value='{{$pemesananlist->id}}' class='form-control input-sm' readonly></td>
								<td hidden><input type='text' name='Quantity[]' id='Quantity' value='{{$pemesananlist->Quantity}}' class='form-control input-sm' readonly></td>
								<td><input type='text' name='Barang[]' id='Barang' value='{{$pemesananlist->Barang}}' class='form-control input-sm' readonly></td>
								<td><input type='text' name='ICode[]' id='ICode' value='{{$pemesananlist->ICode}}' class='form-control input-sm' readonly></td>
								<td><input type='number' name='QTerima[]' id='QTerima' value='{{$pemesananlist->QTerima}}' class='form-control input-sm' placeholder='100' autocomplete required></td>
								@if(env('APP_TYPE')=='Jual')
									<td><input type='text' name='Type[]' id='Type' value='{{$pemesananlist->Type}}' class='form-control input-sm' readonly></td>
								@endif
							</tr>
						@endforeach
					</tbody>
        </table>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
    <!-- col -->
</div>
<!-- row -->

@stop

@section('script')
<script>
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  todayHighlight: true,
	  autoclose: true,
	  //startDate: new Date(),
  }); 
});

$(document).ready(function(){
	//Mask Transport
	$("#Transport").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	
	//Not below zero
	$(document).on('keyup', '#QTerima', function(){
		if(parseInt($(this).closest('tr').find("#QTerima").val()) < 0)
			$(this).closest('tr').find("#QTerima").val(0);
		else if(parseInt($(this).closest('tr').find("#QTerima").val()) > $(this).closest('tr').find("#Quantity").val())
			$(this).closest('tr').find("#QTerima").val($(this).closest('tr').find("#Quantity").val());
		else
			$(this).closest('tr').find("#Quantity").val();
	});
});
</script>
@stop