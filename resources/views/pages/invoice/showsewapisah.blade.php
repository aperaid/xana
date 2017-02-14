@extends('layouts.xana.layout')

@section('title')
	View Invoice Sewa Pisah
@stop

@section('content')
{!! Form::model($invoice, [
  'method' => 'post',
  'route' => ['invoice.updateshowsewapisah', $invoice->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class='form-horizontal'>
        <div class="box-header with-border">
          <h3 class="box-title">Invoice Sewa Pisah Detail</h3>
        </div>
        <!-- box-header -->
        <div class="box-body with-border">
          <div class="col-sm-9">
            <div class="form-group">
              {!! Form::label('No. Invoice', 'No. Invoice', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('Invoice', $invoice->Invoice, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('Project', 'Project', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('Project', $invoice->Project, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('Company', 'Company', ['class' => "col-sm-4 control-label"]) !!}
              <div class="col-sm-8">
                {!! Form::text('Company', $invoice->Company, array('class' => 'form-control', 'readonly')) !!}
              </div>
            </div>
          </div>
          <div class="col-sm-3">
            <table class="table table-bordered table-striped table-responsive">
              <thead>
                <tr>
                  <th>Nomor PO</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{$pocode->POCode}}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <table id="datatables" class="table table-bordered table-striped table-responsive">
            <thead>
              <tr>
                <th align="center">SJ Kirim</th>
                <th align="center">SJ Kembali</th>
                <th align="center">Item</th>
                <th>S</th>
                <th>E</th>
                <th>S-E</th>
                <th>Periode</th>
                <th>I</th>
                <th>Quantity</th>
                <th>PO Discount(%)</th>
                <th>Price/Unit</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              {!! Form::hidden('Invoice', $invoice->Invoice) !!}
              @foreach($periodes as $key => $periode)
              <tr>
                {!! Form::hidden('POCode', $periode->POCode) !!}
                <td>{{$periode->SJKir}}</td>
                <td>{{$periode->SJKem}}</td>
                <td>{{$periode->Barang}}</td>
                <td>{{$periode->S}}</td>
                <td>{{$periode->E}}</td>
                <td>{{$SE[$key]}}</td>
                <td>{{$Days2[$key]}}</td>
                <td>{{$I[$key]}}</td>
                <td>{{$periode->SumQTertanda}}</td>
                <td>{{$periode->Discount}}</td>
                <td>Rp {{ number_format($periode->Amount-($periode->Amount*$periode->Discount/100), 2, ',', '.') }}</td>
                <td>Rp {{ number_format($total2[$key], 2, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!-- Transport Invoice -->
					@if($invoice->PPNT==0)
          <div class="form-group">
            {!! Form::label('TransportInvoice', 'Pisah Invoice Transport', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-8">
              {!! Form::hidden('TransportInvoice', 0) !!}
							@if($invoice->Times==0 && $invoice->TimesKembali==0)
								{!! Form::checkbox('TransportInvoice', 1, $invoice->TransportInvoice, ['id' => 'TransportInvoice', 'class' => 'minimal', 'disabled']) !!}
							@else
								{!! Form::checkbox('TransportInvoice', 1, $invoice->TransportInvoice, ['id' => 'TransportInvoice', 'class' => 'minimal']) !!}
							@endif
            </div>
          </div>
					@else
					@endif
          <!-- Total & Transport & Pajak Input -->
          <div class="form-group">
						{!! Form::label('Total', 'Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Total', 'Rp '.number_format($total, 2, ',','.'), array('id' => 'Total', 'class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::label('Transport', 'Transport', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Transport', 'Rp '. $Transport, ['class' => 'form-control', 'readonly']) !!}
            </div>
						{!! Form::label('Pajak', 'Pajak', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
              {!! Form::text('Pajak', 'Rp '.$Pajak, array('id' => 'Pajak', 'class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Status', 'Transport Status', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-8">
              @if($invoice->TransportInvoice==0)
								@if($invoice->TimesKembali > 0 && $invoice->Times > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & '.$invoice->TimesKembali.' Kali Pengembalian & Transport TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->Times > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & '.$invoice->TimesKembali.' Kali Pengembalian & Transport TIDAK TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->TimesKembali.' Kali Pengembalian & Transport TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->TimesKembali.' Kali Pengembalian & Transport TIDAK TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->Times > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & Transport TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->Times > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & Transport TIDAK TERMASUK PPN & Invoice Transport TIDAK TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @else
                  {!! Form::text('Status', 'Masa Penyewaan Tidak Ada Biaya Transport', ['class' => 'form-control', 'readonly']) !!}
                @endif
              @else
								@if($invoice->TimesKembali > 0 && $invoice->Times > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & '.$invoice->TimesKembali.' Kali Pengembalian & Transport TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->Times > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & '.$invoice->TimesKembali.' Kali Pengembalian & Transport TIDAK TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->TimesKembali.' Kali Pengembalian & Transport TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->TimesKembali > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->TimesKembali.' Kali Pengembalian & Transport TIDAK TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->Times > 0 && $invoice->PPNT == 1)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & Transport TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @elseif($invoice->Times > 0 && $invoice->PPNT == 0)
                  {!! Form::text('Status', $invoice->Times.' Kali Pengiriman & Transport TIDAK TERMASUK PPN & Invoice Transport TERPISAH', ['class' => 'form-control', 'readonly']) !!}
                @else
                  {!! Form::text('Times', '', ['class' => 'form-control', 'readonly']) !!}
                @endif
              @endif
            </div>
          </div>
					<!-- Transport Times -->
          <div class="form-group">
            {!! Form::label('Times', 'Pengiriman', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              <input id="Times" name="Times" type="number" class="form-control" placeholder="Kali" value="{{$invoice->Times}}" >
            </div>
						{!! Form::label('TimesKembali', 'Pengembalian', ['class' => "col-sm-2 control-label"]) !!}
						<div class="col-sm-3">
              <input id="TimesKembali" name="TimesKembali" type="number" class="form-control" placeholder="Kali" value="{{$invoice->TimesKembali}}" >
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
								{!! Form::text('TglTerima', $invoice->TglTerima, ['id' => 'TglTerima', 'class' => 'form-control', 'autocomplete' => 'off']) !!}
							</div>
						</div>
						{!! Form::label('Termin', 'Termin', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
							{!! Form::number('Termin', $invoice->Termin, array('class' => 'form-control', 'placeholder' => 'Hari')) !!}
            </div>
						{!! Form::label('DueDate', 'Due Date', ['class' => "col-sm-1 control-label"]) !!}
            <div class="col-sm-2">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								@if(isset($invoice->TglTerima))
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
              {!! Form::textarea('Catatan', $invoice->Catatan, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '2')) !!}
            </div>
          </div>
          <!-- Discount & Pembulatan Input -->
          <div class="form-group">
						{!! Form::label('Discount', 'Inv Discount(%)', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              <input id="Discount" name="Discount" type="number" class="form-control" placeholder="Percent" value="{{$invoice->Discount}}" onKeyUp="tot()" >
            </div>
            {!! Form::label('Pembulatan', 'Pembulatan (-)', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-3">
              <input id="Pembulatan" name="Pembulatan" type="text" class="form-control" placeholder="Rp. 10,000" value="{{'Rp '. number_format($invoice->Pembulatan,0,',','.')}}" onKeyUp="tot()" >
            </div>
          </div>
          <!-- Grand Total Input -->
          <div class="form-group">
            {!! Form::label('GrandTotal', 'Grand Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-8">
              {!! Form::text('GrandTotal', 'Rp '.$totals, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <!-- Footer Box -->
          <div class="box-footer">
            <!-- Back Button -->
            <a href="{{route('invoice.index')}}"><button type="button" class="btn btn-default">Back</button></a>
            <!-- Print Button -->
            <a href="{{route('invoice.BA', $invoice->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Berita Acara</a>
            <a href="{{route('invoice.Invs', $invoice->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</a>
            @if($invoice->TransportInvoice==1)
              <a href="{{route('invoice.Invst', $invoice->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Transport Invoice</a>
            @endif
            <!-- Submit Button -->
            {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
            <a href="{{route('sjkembali.create', 'id='.$invoice->pocusid)}}"><button type="button" style="margin-right: 5px;"	@if ( $sjkemcheck == 0 ) class="btn btn-default pull-right" disabled @else class="btn btn-warning pull-right"	@endif	>SJ Kembali</button></a>
            <a href="{{route('transaksi.claimcreate', $invoice->pocusid)}}">	<button type="button" style="margin-right: 5px;" @if ( $sjkemcheck == 0 ) class="btn btn-default pull-right" disabled @else class="btn btn-info pull-right" @endif	>Claim</button></a>
          </div>
          <!-- box-footer -->
        </div>
      <!-- box-body -->
      </div>
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
	$("#Amount").maskMoney({prefix:'Rp ', allowZero: true, allowNegative: false, thousands:'.', decimal:',', affixesStay: true, precision: 0});
	//Mask Discount
	$(document).on('keyup', '#Discount', function(){
	if(parseInt($(this).val()) > 100)
		 $(this).val(100);
	else if(parseInt($(this).val()) < 0)
		$(this).val(0);
	});
});

$(function() {
	$('#TglTerima').datepicker({
		format: "dd/mm/yyyy",
		startDate: '{{$periodes->first()->E}}',
		todayHighlight: true,
		autoclose: true
	}); 
});
</script>
@stop