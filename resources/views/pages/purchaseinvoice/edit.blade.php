@extends('layouts.xana.layout')
@section('title')
	View Purchase Invoice
@stop

@section('content')
	{!! Form::model($purchaseinvoice, [
	'method' => 'patch',
	'route' => ['purchaseinvoice.update', $purchaseinvoice->id]
	]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class='form-horizontal'>
        <div class="box-header with-border">
          <h3 class="box-title">Purchase Invoice Detail</h3>
        </div>
        <div class="box-body with-border">
					<div class="col-sm-1">
						@if($purchaseinvoice->TglTerima == '')
							<img src="{{ URL::to('/img/Tgl Terima.PNG') }}" class="img-square">
						@elseif($purchaseinvoice->Lunas == 0)
							<img src="{{ URL::to('/img/Jatuh Tempo.PNG') }}" class="img-square">
						@elseif($purchaseinvoice->Lunas == 1)
							<img src="{{ URL::to('/img/Lunas.PNG') }}" class="img-square">
						@endif
					</div>
          <div class="col-sm-8">
            <div class="form-group">
							<input id="id" type="hidden" value="{{$purchaseinvoice->id}}">
							<input id="Lunas" type="hidden" value="{{$purchaseinvoice->Lunas}}">
              {!! Form::label('PesanCode', 'Pesan Code', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('PesanCode', $purchaseinvoice->PesanCode, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('Invoice', 'No. Invoice', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('Invoice', $purchaseinvoice->PurchaseInvoice, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('Supplier', 'Supplier', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('Supplier', $purchaseinvoice->Company, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
					<div class="col-sm-3">
						<a href="{{route('pemesanan.show', $purchaseinvoice->idPesan)}}"><button type="button" class="btn btn-success btn-block">View</button></a>
					</div>
          <table id="datatables" class="table table-bordered table-striped table-responsive">
            <thead>
              <tr>
								<th>Type</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price/Unit</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              @foreach($purchaseinvoices as $purchaseinvoices)
              <tr>
								<td>{{$purchaseinvoices->Type}}</td>
                <td>{{$purchaseinvoices->Barang}}</td>
                <td>{{$purchaseinvoices->QTerima}}</td>
                <td>Rp {{ number_format($purchaseinvoices->Amount, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($purchaseinvoices->QTerima*$purchaseinvoices->Amount, 2,',','.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
					<hr>
          <!-- Total -->
					<div class="form-group">
						{!! Form::label('Total', 'Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-8">
              {!! Form::text('Total', 'Rp '.number_format($purchaseinvoice->Total, 2, ',','.'), array('id' => 'Total', 'class' => 'form-control', 'readonly')) !!}
            </div>
					</div>
					<!-- Termin Input -->
					<div class="form-group ">
						{!! Form::label('TglTerima', 'Tgl Terima Surat', ['class' => "col-sm-2 control-label"]) !!}
						<div class="col-sm-2">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								{!! Form::text('TglTerima', $purchaseinvoice->TglTerima, ['id' => 'TglTerima', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
							</div>
						</div>
						{!! Form::label('Termin', 'Termin Hari', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
							{!! Form::number('Termin', $purchaseinvoice->Termin, array('class' => 'form-control', 'placeholder' => 'Hari')) !!}
            </div>
						{!! Form::label('DueDate', 'Due Date', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								@if(isset($purchaseinvoice->TglTerima))
									{!! Form::text('DueDate', $duedate, array('class' => 'form-control', 'readonly')) !!}
								@else
									{!! Form::text('DueDate', null, array('class' => 'form-control', 'readonly')) !!}
								@endif
							</div>
						</div>
					</div>
          <!-- Catatan Input -->
          <div class="form-group">
            {!! Form::label('Catatan', 'Catatan', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-8">
              {!! Form::textarea('Catatan', $purchaseinvoice->Catatan, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '4')) !!}
            </div>
          </div>
          <!-- Discount & Pembulatan Input -->
          <div class="form-group">
						{!! Form::label('Discount', 'Inv Discount (-)', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              <input id="Discount" name="Discount" type="text" class="form-control" placeholder="15" value="{{'Rp '. number_format($purchaseinvoice->Discount,0,',','.')}}" onKeyUp="tot()" autocomplete="off">
            </div>
            {!! Form::label('Pembulatan', 'Pembulatan (-)', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              <input id="Pembulatan" name="Pembulatan" type="text" class="form-control" placeholder="Rp. 10,000" value="{{'Rp '. number_format($purchaseinvoice->Pembulatan,0,',','.')}}" onKeyUp="tot()" autocomplete="off">
            </div>
						{!! Form::label('TransportRetur', 'Transport Retur (-)', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
							{!! Form::text('TransportRetur', 'Rp '.number_format($purchaseinvoice->TransportRetur, 2, ',','.'), ['class' => 'form-control', 'readonly']) !!}
            </div>
          </div>
          <!-- Grand Total Input -->
          <div class="form-group">
            {!! Form::label('Transport', 'Transport', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
							{!! Form::text('Transport', 'Rp '.number_format($purchaseinvoice->TransportTerima, 2, ',','.'), ['class' => 'form-control', 'readonly']) !!}
            </div>
            {!! Form::label('GrandTotal', 'Grand Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              {!! Form::text('GrandTotal', 'Rp '.number_format($GrandTotal, 2, ',','.'), array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="box-footer">
            <!-- Back Button -->
            <a href="{{route('purchaseinvoice.index')}}"><button type="button" class="btn btn-default">Back</button></a>
            <a href="{{route('invoice.Invj', $purchaseinvoice->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</a>
            <!-- Submit Button -->
            {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
						@if($purchaseinvoice->TglTerima=='')
							<button type="button" class="btn btn-danger pull-right" style="margin-right: 5px;" disabled>Belum Lunas</button>
						@elseif($purchaseinvoice->Lunas==0)
							<button type="button" class="btn btn-danger pull-right lunas" style="margin-right: 5px;">Belum Lunas</button>
						@else
							<button type="button" class="btn btn-success pull-right lunas" style="margin-right: 5px;">Lunas</button>
						@endif
          </div>
          <!-- box-footer -->
        </div>
        <!-- box-body -->
      </div>
      <!-- form -->
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
function tot(){
  var txtFirstNumberValue = document.getElementById('Total2').value;
  var txtSecondNumberValue = document.getElementById('PPN').value;
	var txtThirdNumberValue = document.getElementById('Transport').value;
	var txtFourthNumberValue = document.getElementById('Discount').value;
	var result = (parseFloat(txtFirstNumberValue) * parseFloat(txtSecondNumberValue)*0.1)+parseFloat(txtFirstNumberValue) + parseFloat(txtThirdNumberValue) - parseFloat(txtFourthNumberValue);
	if (!isNaN(result)) {
		document.getElementById('Total').value = result;
    }
}

$(document).ready(function(){
	//Mask Price
	$("#Pembulatan").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	$("#Discount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	$("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	//Mask Discount
	/*$(document).on('keyup', '#Discount', function(){
	if(parseInt($(this).val()) > 100)
		 $(this).val(100);
	else if(parseInt($(this).val()) < 0)
		$(this).val(0);
	});*/
});

$(".lunas").click(function(){
  $.post("../../purchaseinvoice/updatelunas", {"_token":"{{csrf_token()}}", id: $("#id").val(), LunasType: $("#Lunas").val()}, function(){})
  .done(function(data){
    location.reload();
  })
  .fail(function(data){
    console.log('fail');
  });
});

$(function() {
	$('#TglTerima').datepicker({
		format: "dd/mm/yyyy",
		todayHighlight: true,
		autoclose: true
	}); 
});
</script>
@stop