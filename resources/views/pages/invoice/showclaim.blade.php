@extends('layouts.xana.layout')
@section('title')
	View Invoice Claim
@stop

@section('content')
{!! Form::model($invoice, [
  'method' => 'post',
  'route' => ['invoice.updateshowclaim', $invoice->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-info">
      <div class='form-horizontal'>
        <div class="box-header with-border">
          <h3 class="box-title">Invoice Detail</h3>
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
                  <th>Nomor PO Terakhir</th>
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
                <th align="center">SJKir</th>
                <th align="center">Item</th>
                <th>Tgl Claim</th>
                <th>Quantity Claim</th>
                <th>Price/Unit</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              {!! Form::hidden('Invoice', $invoice->Invoice) !!}
              @foreach($transaksis as $key => $transaksi)
              <tr>
                {!! Form::hidden('Claim', $transaksi->Claim) !!}
                <td>{{$transaksi->SJKir}}</td>
                <td>{{$transaksi->Barang}}</td>
                <td>{{$transaksi->Tgl}}</td>
                <td>{{$transaksi->QClaim}}</td>
                <td>Rp {{ number_format($transaksi->Amount, 2, ',', '.') }}</td>
                <td>Rp {{ number_format($total2[$key], 2, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <!-- PPN checkbox -->
          <div class="form-group">
            @if(Auth::user()->access == 'Admin')
              {!! Form::label('PPN', 'Pajak 10%', ['class' => "col-sm-2 control-label"]) !!}
              <div class="col-sm-6">
                {!! Form::hidden('PPN', 0) !!}
                {!! Form::checkbox('PPN', 1, $invoice->PPN, ['id' => 'PPN', 'class' => 'minimal']) !!}
              </div>
            @endif
          </div>
          <!-- Total Input -->
          <div class="form-group">
            {!! Form::label('Total', 'Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-7">
              {!! Form::text('Total', 'Rp '.number_format($total, 2, ',','.'), array('id' => 'Total', 'class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <!-- Pajak Input -->
          <div class="form-group">
            {!! Form::label('Pajak', 'Pajak', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-7">
              {!! Form::text('Pajak', 'Rp '.$Pajak, array('id' => 'Pajak', 'class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          <!-- Discount Input -->
          <div class="form-group">
            {!! Form::label('Discount', 'Inv Discount(%)', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              <input id="Discount" name="Discount" type="number" class="form-control" placeholder="15" value="{{$invoice->Discount}}" onKeyUp="tot()" >
            </div>
          </div>
          <!-- Catatan Input -->
          <div class="form-group">
            {!! Form::label('Catatan', 'Catatan', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::textarea('Catatan', $invoice->Catatan, array('class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Catatan', 'rows' => '5')) !!}
            </div>
          </div>
          <!-- Pembulatan Input -->
          <div class="form-group">
            {!! Form::label('Pembulatan', 'Pembulatan (-)', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              <input id="Pembulatan" name="Pembulatan" type="text" class="form-control" placeholder="Rp. 10,000" value="{{'Rp '. number_format($invoice->Pembulatan,0,',','.')}}" onKeyUp="tot()" >
            </div>
          </div>
          <!-- Grand Total Input -->
          <div class="form-group">
            {!! Form::label('GrandTotal', 'Grand Total', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-7">
              {!! Form::text('GrandTotal', 'Rp '.$totals, array('class' => 'form-control', 'readonly')) !!}
            </div>
          </div>
          {!! Form::hidden('Total2', round($total, 2), array('id' => 'Total2', 'class' => 'form-control')) !!}
          <div class="box-footer">
            <!-- Back Button -->
            <a href="{{route('invoice.index')}}"><button type="button" class="btn btn-default">Back</button></a>
            <a href="{{route('invoice.Invc', $invoice->id)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print Invoice</a>
            <!-- Submit Button -->
            {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
          </div>
          <!-- box-footer -->
        </div>
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
	var txtThirdNumberValue = document.getElementById('Discount').value;
	var result = (parseFloat(txtFirstNumberValue) * parseFloat(txtSecondNumberValue)*0.1)+parseFloat(txtFirstNumberValue) - parseFloat(txtThirdNumberValue);
	if (!isNaN(result)) {
		//document.getElementById('Total').value = result;
    }
}
</script>
<script>
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
</script>
@stop