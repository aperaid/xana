@extends('layouts.xana.layout')
@section('title')
	View Invoice Jual
@stop

@section('content')
{!! Form::model($invoice, [
  'method' => 'post',
  'route' => ['invoice.updateshowjual', $invoice->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class='form-horizontal'>
        <div class="box-header with-border">
          <h3 class="box-title">Invoice Jual Detail</h3>
        </div>
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
          <div class="col-md-3">
            <table class="table table-bordered table-striped table-responsive">
              <thead>
                <tr>
                  <th>Nomor PO</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pocodes as $pocode)
                <tr>
                  <td>{{$pocode->POCode}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <table id="datatables" class="table table-bordered table-striped table-responsive">
            <thead>
              <tr>
                <th align="center">SJ Kirim</th>
                <th align="center">Item</th>
                <th>Quantity Kirim</th>
                <th>Price/Unit</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              {!! Form::hidden('Invoice', $invoice->Invoice) !!}
              @foreach($transaksis as $key => $transaksi)
              <tr>
                {!! Form::hidden('POCode', $transaksi->POCode) !!}
                <td>{{$transaksi->SJKir}}</td>
                <td>{{$transaksi->Barang}}</td>
                <td>{{$transaksi->QKirim}}</td>
                <td>Rp {{ number_format($transaksi->Amount, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($total2[$key], 2,',','.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!-- PPN checkbox -->
          <div class="form-group">
            <label class="col-sm-2 control-label">Pajak 10%</label>
            <div class="col-sm-6">
              {!! Form::hidden('PPN', 0) !!}
              {!! Form::checkbox('PPN', 1, $invoice->PPN, array('class' => 'minimal')) !!}
            </div>
          </div>
          <!-- Transport Input -->
          <div class="form-group">
            {!! Form::label('Transport', 'Transport', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              <input name="Transport" type="text" class="form-control" value="{{'Rp '. number_format($invoice->Transport,0,',','.')}}" disabled >
            </div>
          </div>
          <div class="form-group">
            {!! Form::label('Transport Status', 'Transport Status', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              @if($invoice->PPNT == 1)
                {!! Form::text('Times', $invoice->Times.' Kali Pengiriman & Transport TERMASUK PPN', ['class' => 'form-control', 'readonly']) !!}
              @else
                {!! Form::text('Times', $invoice->Times.' Kali Pengiriman & Transport TIDAK TERMASUK PPN', ['class' => 'form-control', 'readonly']) !!}
              @endif
            </div>
          </div>
          <!-- Discount Input -->
          <div class="form-group">
            {!! Form::label('Discount', 'Discount', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              <input id="Discount" name="Discount" type="text" class="form-control" placeholder="Rp. 10,000" value="{{'Rp '. number_format($invoice->Discount,0,',','.')}}" onKeyUp="tot()" >
            </div>
          </div>
          <!-- Catatan Input -->
          <div class="form-group">
            {!! Form::label('Catatan', 'Catatan', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::textarea('Catatan', $invoice->Catatan, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5')) !!}
            </div>
          </div>
          <!-- Total Text -->
          <div class="form-group">
            {!! Form::label('Total', 'Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              @if($invoice->PPNT == 1)
                {!! Form::text('Total', 'Rp. ' . number_format((($total+($invoice->Transport*$invoice->Times))*$invoice->PPN*0.1)+($total+($invoice->Transport*$invoice->Times))-$invoice->Discount, 2, ',','.'), array('class' => 'form-control', 'readonly')) !!}
              @else
                {!! Form::text('Total', 'Rp. ' . number_format(($total*$invoice->PPN*0.1)+$total+($invoice->Transport*$invoice->Times)-$invoice->Discount, 2, ',','.'), array('class' => 'form-control', 'readonly')) !!}
              @endif
            </div>
            {!! Form::hidden('Total2', round($total, 2), array('id' => 'Total2', 'class' => 'form-control')) !!}
          </div>
          <div class="box-footer">
            <!-- Back Button -->
            <a href="{{route('invoice.index')}}"><button type="button" class="btn btn-default">Back</button></a>
            <!-- Submit Button -->
            {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
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