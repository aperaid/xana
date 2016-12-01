@extends('layouts.xana.layout')
@section('title')
	View PO
@stop

@section('content')
	{!! Form::open([
	'method' => 'delete',
	'route' => ['po.destroy', $po->id]
	]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('reference.show', $id -> id )}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
        <a href="{{route('po.edit', $po -> id )}}"><button type="button" class="btn btn-primary pull-right" @if ($pocheck == 1) disabled @endif >Edit</button></a>
        <button type="submit" class="btn btn-danger pull-right" style="margin-right: 5px;" @if($pocheck == 1) disabled @endif onclick="return confirm('Delete PO?')">Delete</button>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

<!-- CONTENT ROW -->
<div class="row">
  <!-- PO INFO BOX -->
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Nomor PO', 'Nomor PO') !!}
          {!! Form::text('POCode', $po -> POCode, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $po -> Tgl, array('class' => 'form-control', 'readonly')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Catatan', 'Catatan') !!}
          {!! Form::textarea('Catatan', $po -> Catatan, array('class' => 'form-control', 'readonly')) !!}
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <!-- POITEM BOX -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">PO Item</h3>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>Barang</th>
              <th>Type</th>
              <th>J/S</th>
              <th>Quantity</th>
              <th>Price/Unit</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transaksis as $transaksi)
            <tr>
              <td>{{ $transaksi -> Barang }}</td>
              <td>{{ $transaksi -> Type }}</td>
              <td>{{ $transaksi -> JS }}</td>
              <td>{{ $transaksi -> Quantity }}</td>
              <td>{{ 'Rp '. number_format( $transaksi -> Amount, 2,',', '.' ) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- box -->
  </div>
    <!-- col -->
</div>
<!-- row -->

{!! Form::close() !!}
@stop