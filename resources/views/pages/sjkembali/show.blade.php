@extends('layouts.xana.layout')
@section('title')
	View SJ Kembali
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['sjkembali.destroy', $sjkembali->id]
]) !!}
<section class="invoice">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="page-header">
        <i class="fa fa-globe"></i> SJ Kembali | {{ $sjkembali -> SJKem }}
        <small class="pull-right">Date: {{ $sjkembali -> Tgl }}</small>
      </h2>
    </div>
  </div>
  
  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      Company
      <address>
        <strong>{{ $isisjkembali -> Company }}</strong><br>
        {{ $isisjkembali -> CompAlamat }}<br>
        {{ $isisjkembali -> CompKota }},  {{ $isisjkembali -> CompZip }}<br>
        Phone: {{ $isisjkembali -> CompPhone }}<br>
        Email: {{ $isisjkembali -> CompEmail }}
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      Project
      <address>
        <strong>{{ $isisjkembali -> Project }}</strong><br>
        {{ $isisjkembali -> ProjAlamat }}<br>
        {{ $isisjkembali -> ProjKota }},  {{ $isisjkembali -> ProjZip }}<br>
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      Contact Person
      <address>
        <strong>{{ $isisjkembali -> Customer }}</strong><br>
        Phone: {{ $isisjkembali -> CustPhone }}<br>
        Email: {{ $isisjkembali -> CustEmail }}
      </address>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table id="datatables" class="table table-striped">
        <thead>
          <tr>
            <th>Tanggal Kirim</th>
            <th>Barang</th>
            <th>Warehouse</th>
            <th>Q Pengambilan</th>
            <th>Q Terima</th>
          </tr>
        </thead>
        <tbody>
          @foreach($isisjkembalis as $isisjkembali)
          <tr>
            <td>{{ $isisjkembali->Tgl }}</td>
            <td>{{ $isisjkembali->Barang }}</td>
            <td>{{ $isisjkembali->Warehouse }}</td>
            <td>{{ $isisjkembali->QTertanda2 }}</td>
            <td>{{ $isisjkembali->QTerima2 }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
        
    <div class="box-footer">
      <a href="{{route('sjkembali.index')}}"><button type="button" class="btn btn-default">Back</button></a>
      <a href="#"><button type="button" class="btn btn-default">Print</button></a>
      <a href="EditSJKembaliQuantity.php?SJKem={{ $isisjkembali->SJKem }}"><button type="button" class="btn btn-success pull-right">Quantity Terima</button></a>
      <a href="EditSJKembali.php?SJKem={{ $isisjkembali->SJKem }}&Reference={{ $isisjkembali->Reference }}"><button type="button" @if($qtrimacheck == 0) style="margin-right: 5px" class="btn btn-primary pull-right" @else style="margin-right: 5px" class="btn btn-default pull-right" disabled @endif >Edit Pengembalian</button></a>
      <a href="#"><button type="button" style="margin-right: 5px" class="btn btn-danger pull-right">Delete</button></a>
    </div>
    <!-- box footer -->
  </div>
  <!-- row -->
</section>
<!-- invoice -->
{!! Form::close() !!}
@stop