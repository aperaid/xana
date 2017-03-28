@extends('layouts.xana.layout')
@section('title')
	View Stock Project
@stop

@section('content')
<section class="invoice">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="page-header">
        <i class="fa fa-globe"></i> View Stock Project | {{ $transaksi->PCode }}
        <small class="pull-right">Date: {{ $transaksi -> Tgl }}</small>
      </h2>
    </div>
  </div>

  <div class="row invoice-info">
    <div class="col-sm-6 invoice-col">
      Company
      <address>
        <strong>{{ $transaksi -> Company }}</strong><br>
        {{ $transaksi -> CompAlamat }}<br>
        {{ $transaksi -> CompKota }},  {{ $transaksi -> CompZip }}<br>
        Phone: {{ $transaksi -> CompPhone }}<br>
        Email: {{ $transaksi -> CompEmail }}
      </address>
    </div>
    <div class="col-sm-6 invoice-col">
      Project
      <address>
        <strong>{{ $transaksi -> Project }}</strong><br>
        {{ $transaksi -> ProjAlamat }}<br>
        {{ $transaksi -> ProjKota }},  {{ $transaksi -> ProjZip }}<br>
				Sales: {{ $transaksi -> Sales }}
      </address>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table id="datatables" class="table table-striped">
        <thead>
          <tr>
            <th>J/S</th>
            <th>Barang</th>
            <th>Q Total</th>
            <th>Q di Proyek</th>
          </tr>
        </thead>
        <tbody>
          @foreach($transaksis as $transaksi)
          <tr>
            <td>{{ $transaksi->JS }}</td>
            <td>{{ $transaksi->Barang }}</td>
            <td>{{ $transaksi->Quantity }}</td>
            <td>{{ $transaksi->QSisaKem }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    <div class="box-footer">
      <a href="{{route('inventory.stockproject')}}"><button type="button" class="btn btn-default">Back</button></a>
    </div>
    <!-- box footer -->
  </div>
  <!-- row -->
</section>
<!-- invoice -->
@stop

@section('script')
<script>

</script>
@stop