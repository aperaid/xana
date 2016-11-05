@extends('layouts.xana.layout')
@section('title')
	View Invoice Jual
@stop

@section('content')
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
            <table class="table table-bordered">
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
                <th>Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php $x = 0 ?>
              @foreach($transaksis as $transaksi)
              <tr>
                {!! Form::hidden('POCode', $transaksi->POCode) !!}
                <td>{{$transaksi->SJKir}}</td>
                <td>{{$transaksi->Barang}}</td>
                <td>{{$transaksi->QKirim}}</td>
                <td>Rp {{ number_format($transaksi->Amount, 2, ',', '.') }}</td>
                <td>Rp <?php echo number_format($total2[$x], 2,',','.') ?></td>
              </tr>
              <?php $x++ ?>
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
              {!! Form::text('Transport', 'Rp '. number_format($transport,0,',','.'), array('id' => 'Transport', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 100,000', 'onkeyup' => 'tot()')) !!}
            </div>
          </div>
          <!-- Discount Input -->
          <div class="form-group">
            {!! Form::label('Discount', 'Discount', ['class' => "col-sm-2 control-label"]) !!}
            <div class="col-sm-6">
              {!! Form::text('Discount', 'Rp '. number_format($invoice->Discount,0,',','.'), array('id' => 'Discount', 'class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Rp. 10.000', 'onkeyup' => 'tot()')) !!}
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
              {!! Form::text('Total', 'Rp. ' . number_format(($total*$invoice->PPN*0.1)+$total+$transport-$invoice->Discount, 2, ',','.'), array('class' => 'form-control', 'readonly')) !!}
            </div>
            {!! Form::hidden('Total2', round($total, 2), array('id' => 'Total2', 'class' => 'form-control')) !!}
          </div>
          <div class="box-footer">
            <!-- Back Button -->
            <a href="{{route('invoice.index')}}"><button type="button" class="btn btn-default">Back</button></a>
            <!-- Print Button -->
            <a href="#"><button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print</button></a>
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
@stop