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
    <div class="col-sm-3 invoice-col">
      Company
      <address>
        <strong>{{ $isisjkirim -> Company }}</strong><br>
        {{ $isisjkirim -> CompAlamat }}<br>
        {{ $isisjkirim -> CompKota }},  {{ $isisjkirim -> CompZip }}<br>
        Phone: {{ $isisjkirim -> CompPhone }}<br>
        Email: {{ $isisjkirim -> CompEmail }}
      </address>
    </div>
    <div class="col-sm-3 invoice-col">
      Project
      <address>
        <strong>{{ $isisjkirim -> Project }}</strong><br>
        {{ $isisjkirim -> ProjAlamat }}<br>
        {{ $isisjkirim -> ProjKota }},  {{ $isisjkirim -> ProjZip }}<br>
      </address>
    </div>
    <div class="col-sm-3 invoice-col">
      Contact Person
      <address>
        <strong>{{ $isisjkirim -> Customer }}</strong><br>
        Phone: {{ $isisjkirim -> CustPhone }}<br>
        Email: {{ $isisjkirim -> CustEmail }}
      </address>
    </div>
    <div class="col-sm-3 invoice-col">
      No Polisi
      <address>
        <strong>{{ $isisjkirim -> NoPolisi }}</strong><br>
        Sopir: {{ $isisjkirim -> Sopir }}<br>
        Kenek: {{ $isisjkirim -> Kenek }}<br>
				Periode: {{ $isisjkirim -> Periode }}
      </address>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 table-responsive">
      <table id="datatables" class="table table-striped">
				{!! Form::hidden('Periode', $isisjkirim->Periode) !!}
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
    <div class="box-body">
      <div class="form-group">
        {!! Form::label('Keterangan', 'Keterangan', ['class' => "col-sm-1 control-label"]) !!}
        <div class="col-sm-5">
          {!! Form::textarea('Keterangan', $isisjkirim->Keterangan, array('class' => 'form-control', 'rows' => '3', 'readonly')) !!}
        </div>
        {!! Form::label('FormMuat', 'Form Muat', ['class' => "col-sm-1 control-label"]) !!}
        <div class="col-sm-5">
          {!! Form::textarea('FormMuat', $isisjkirim->FormMuat, array('class' => 'form-control', 'rows' => '3', 'readonly')) !!}
        </div>
      </div>
    </div>
    
    <div class="box-footer">
      <a href="{{route('sjkirim.index')}}"><button type="button" class="btn btn-default">Back</button></a>
      <a href="{{route('sjkirim.SJ', $sjkirim->sjkirid)}}" button type="button" class="btn btn-default"><i class="fa fa-print"></i> Print SJ</a>
      @if($isisjkirim->QKirim!=$isisjkirim->QTertanda)
      <a href="{{route('sjkirim.baranghilang', $sjkirim->sjkirid)}}" button type="button" class="btn btn-danger">Barang Hilang</a>
      @endif
      
      <a href="{{route('sjkirim.qtertanda', $sjkirim->id)}}"><button type="button" @if ($qttdcheck > 0) class="btn btn-default pull-right" disabled @else class="btn btn-success pull-right" @endif >Q Tertanda</button></a>
      <a href="{{route('sjkirim.edit', $sjkirim->id)}}"><button type="button" @if ($jumlah > 0) style="margin-right: 5px" class="btn btn-default pull-right" disabled @else style="margin-right: 5px" class="btn btn-primary pull-right" @endif >Edit Pengiriman</button></a>
      <button type="submit" class="btn btn-danger pull-right" style="margin-right: 5px;" @if($jumlah > 0) disabled @endif onclick="return confirm('Delete SJ Kirim?')">Delete</button>
    </div>
    <!-- box footer -->
  </div>
  <!-- row -->
</section>
<!-- invoice -->
{!! Form::close() !!}
@stop