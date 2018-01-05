@extends('layouts.xana.layout')
@section('title')
	Create Penerimaan
@stop

@section('content')
	{!! Form::open([
  'route' => 'penerimaan.store'
  ]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('penerimaan.index')}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
			</div>
			<!-- box-body -->
		</div>
		<!-- box -->
	</div>
	<!-- col -->
</div>
<!-- row -->
  
<!-- CONTENT ROW -->
<div class="row">
  <div class="col-md-3">
    <div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Penerimaan Detail</h3>
			</div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Tgl', 'Date') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', null, array('id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => '31/12/2000', 'required')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Transport', 'Biaya Transport') !!}
          {!! Form::text('Transport', 'Rp 0', array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100.000')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('PesanCode', 'Pesan Code') !!}
          {!! Form::text('PesanCode', $pemesanans[0]->PesanCode, array('class' => 'form-control', 'id' => 'PesanCode', 'readonly')) !!}
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <div class="col-md-9">
    <!-- general form elements -->
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Input Pemesanan Item</h3>
      </div>
      <div class="box-body">
				<table class="table table-hover table-bordered" id="customFields">
					<thead>
						<tr>
							<th hidden>Id</th>
							<th>Barang</th>
							<th>ICode</th>
							<th width="10%">QTerima</th>
							@if(env('APP_TYPE')=='Jual')
								<th width="30%">Kategori</th>
							@endif
						</tr>
					</thead>
					<tbody>
            @foreach($pemesanans as $pemesanan)
							<tr class='tr_input'>
								<td hidden><input type='text' name='Id[]' id='Id' value='{{$pemesanan->id}}' class='form-control input-sm' readonly></td>
								<td><input type='text' name='Barang[]' id='Barang' value='{{$pemesanan->Barang}}' class='form-control input-sm' readonly></td>
								<td><input type='text' name='ICode[]' id='ICode' value='{{$pemesanan->ICode}}' class='form-control input-sm' readonly></td>
								<td><input type='number' name='QTerima[]' id='QTerima' value='{{$pemesanan->Quantity}}' class='form-control input-sm' required></td>
								@if(env('APP_TYPE')=='Jual')
									<td><input type='text' name='Type[]' id='Type' value='{{$pemesanan->Type}}' class='form-control input-sm' readonly></td>
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

{!! Form::close() !!}
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
	});
});
</script>
@stop