@extends('layouts.xana.layout')
@section('title')
	View SJ Kirim
@stop

@section('content')
{!! Form::open([
  'method' => 'delete',
  'route' => ['sjkirim.destroy', $sjkirim->id]
]) !!}
<section class="invoice">
  <div class="row">
    <div class="col-xs-12">
      <h2 class="page-header">
        <i class="fa fa-globe"></i> SJ Kirim | {{ $sjkirim -> SJKir }}
        <small class="pull-right">Date: {{ $sjkirim -> Tgl }}</small>
      </h2>
    </div>
  </div>

  <div class="row invoice-info">
    <div class="col-sm-4 invoice-col">
      Company
      <address>
        <strong>{{ $isisjkirim -> Company }}</strong><br>
        {{ $isisjkirim -> Alamat }}<br>
        {{ $isisjkirim -> Kota }},  {{ $isisjkirim -> Zip }}<br>
        Phone: {{ $isisjkirim -> CompPhone }}<br>
        Email: {{ $isisjkirim -> CompEmail }}
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      Project
      <address>
        <strong>{{ $isisjkirim -> Project }}</strong><br>
        {{ $isisjkirim -> Alamat }}<br>
        {{ $isisjkirim -> Kota }},  {{ $isisjkirim -> Zip }}<br>
      </address>
    </div>
    <div class="col-sm-4 invoice-col">
      Contact Person
      <address>
        <strong>{{ $isisjkirim -> Customer }}</strong><br>
        Phone: {{ $isisjkirim -> CustPhone }}<br>
        Email: {{ $isisjkirim -> CustEmail }}
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
            <th>Warehouse</th>
            <th>Q Kirim</th>
            <th>Q Tertanda</th>
          </tr>
        </thead>
        <tbody>
          @foreach($isisjkirims as $isisjkirim)
          <tr>
            <td>{{ $isisjkirim->JS }}</td>
            <td>{{ $isisjkirim->Barang }}</td>
            <td>{{ $isisjkirim->Warehouse }}</td>
            <td>{{ $isisjkirim->QKirim }}</td>
            <td>{{ $isisjkirim->QTertanda }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    <div class="box-footer">
      <a href="{{route('sjkirim.index')}}"><button type="button" class="btn btn-default">Back</button></a>
      <a href="#"><button type="button" class="btn btn-default">Print</button></a>
      <a href="EditSJKirimQuantity.php?SJKir={{ $isisjkirim->SJKir }}&Periode={{ $isisjkirim->Periode }}&Reference={{ $isisjkirim->Reference }}"><button type="button" @if ($qttdcheck > 0) class="btn btn-default pull-right" disabled @else class="btn btn-success pull-right" @endif >Q Tertanda</button></a>
      <a href="{{route('sjkirim.edit', $isisjkirim->SJKir)}}"><button type="button" @if ($jumlah > 0) style="margin-right: 5px" class="btn btn-default pull-right" disabled @else style="margin-right: 5px" class="btn btn-primary pull-right" @endif >Edit Pengiriman</button></a>
      <a href="#"><button type="button" style="margin-right: 5px" class="btn btn-danger pull-right">Delete</button></a>
    </div>
    <!-- box footer -->
  </div>
  <!-- row -->
</section>
<!-- invoice -->
{!! Form::close() !!}
@stop