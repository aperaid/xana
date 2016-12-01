@extends('layouts.xana.layout')
@section('title')
	View Penawaran
@stop

@section('content')
	{!! Form::open([
	'method' => 'delete',
	'route' => ['penawaran.destroy', $penawaran->id]
	]) !!}

<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <a href="{{route('penawaran.index')}}"><button type="button" class="btn btn-default pull-left">Back</button></a>
        <a href="{{route('penawaran.edit', $penawaran -> id )}}"><button type="button" class="btn btn-primary pull-right">Edit</button></a>
        <button type="submit" class="btn btn-danger pull-right" style="margin-right: 5px;" onclick="return confirm('Delete Penawaran?')">Delete</button>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
</div>
<!-- row -->

<div class="row">
  <div class="col-md-3">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Penawaran Detail</h3>
      </div>
      <div class="box-body">
        <div class="form-group">
          {!! Form::label('Penawaran Code', 'Penawaran Code') !!}
          {!! Form::text('Penawaran', $penawaran -> Penawaran, array('class' => 'form-control', 'readonly')) !!}
        </div>
        <div class="form-group">
          {!! Form::label('Tanggal', 'Tanggal') !!}
          <div class="input-group">
            <div class="input-group-addon">
              <i class="fa fa-calendar"></i>
            </div>
            {!! Form::text('Tgl', $penawaran -> Tgl, array('class' => 'form-control', 'readonly')) !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('Project Code', 'Project Code') !!}
          {!! Form::text('PCode', $penawaran -> PCode, array('class' => 'form-control', 'readonly')) !!}
        </div>
      </div>
      <!-- box-body -->
    </div>
    <!-- box -->
  </div>
  <!-- col -->
  <div class="col-md-9">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Penawaran Item</h3>
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
            @foreach($penawarans as $penawaran)
            <tr>
              <td>{{ $penawaran -> Barang }}</td>
              <td>{{ $penawaran -> Type }}</td>
              <td>{{ $penawaran -> JS }}</td>
              <td>{{ $penawaran -> Quantity }}</td>
              <td>{{ 'Rp '. number_format( $penawaran -> Amount, 2,',', '.' ) }}</td>
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