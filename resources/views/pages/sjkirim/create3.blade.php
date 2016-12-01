@extends('layouts.xana.layout')
@section('title')
	Create SJKirim
@stop

@section('content')
{!! Form::open([
  'route' => 'sjkirim.store'
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>J/S</th>
              <th>Barang</th>
              <th>Warehouse</th>
              <th>Q Sisa Kirim</th>
              <th>Q Kirim</th>
            </tr>
          </thead>
          <tbody>
            {!! Form::hidden('sjkirimid', $sjkirim->maxid+1) !!}
            {!! Form::hidden('tglE', $tglE) !!}
            {!! Form::hidden('Periode', $periode) !!}
            @foreach($transaksis as $key => $transaksi)
            <tr>
              {!! Form::hidden('id[]', $transaksi->id) !!}
              {!! Form::hidden('isisjkirimid[]', $isisjkirim->maxid+$key+1) !!}
              {!! Form::hidden('IsiSJKir[]', $maxisisjkir->IsiSJKir+$key+1) !!}
              {!! Form::hidden('periodeid[]', $maxperiode->maxid+$key+1) !!}
              {!! Form::hidden('Reference[]', $transaksi->Reference) !!}
              {!! Form::hidden('Purchase[]', $transaksi->Purchase) !!}
              {!! Form::hidden('ICode[]', $transaksi->ICode) !!}
              <td>{!! Form::text('JS[]', $transaksi->JS, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Barang[]', $transaksi->Barang, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Warehouse[]', $transaksi->Warehouse, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('QSisaKirInsert[]', $transaksi->QSisaKirInsert, array('class' => 'form-control', 'readonly')) !!}</td>
              <td><input name="QKirim[]" type="number" class="form-control" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $transaksi->QSisaKirInsert }})" value="{{ $transaksi->QSisaKirInsert }}" required></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <table id="datatables" class="table table-bordered table-striped table-responsive">
        <thead>
          <tr>
            <th>No Polisi</th>
            <th>Sopir</th>
            <th>Kenek</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{!! Form::text('NoPolisi', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}</td>
            <td>{!! Form::text('Sopir', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}</td>
            <td>{!! Form::text('Kenek', null, array('class' => 'form-control', 'autocomplete' => 'off', 'required')) !!}</td>
          </tr>
        </tbody>
      </table>
      <div class="box-footer">
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
        <a href="{{route('sjkirim.create', 'id='.$referenceid->id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
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
function minmax(value, min, max) 
{
	if(parseInt(value) < min || isNaN(value)) 
    return 0; 
  if(parseInt(value) > max) 
    return parseInt(max); 
  else return value;
}
</script>
@stop