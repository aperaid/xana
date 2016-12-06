@extends('layouts.xana.layout')
@section('title')
	Create SJKembali
@stop

@section('content')
{!! Form::open([
  'route' => 'sjkembali.store'
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body">
        <table id="datatables" class="table table-bordered table-striped table-responsive">
          <thead>
            <tr>
              <th>Extend Date</th>
					    <th>Barang</th>
					    <th>Warehouse</th>
					    <th>Q Sisa Kembali</th>
					    <th>Q Pengambilan</th>
            </tr>
          </thead>
          <tbody>
            {!! Form::hidden('sjkembaliid', $sjkembali->maxid+1) !!}
            @foreach($isisjkirims as $key => $isisjkirim)
            <tr>
              {!! Form::hidden('id[]', $isisjkirim->id) !!}
              {!! Form::hidden('Periode', $isisjkirim->Periode) !!}
              {!! Form::hidden('isisjkembaliid', $isisjkembali->maxid+$key+1) !!}
              {!! Form::hidden('IsiSJKem', $maxisisjkem->IsiSJKem+$key+1) !!}
              {!! Form::hidden('Purchase[]', $isisjkirim->Purchase) !!}
              {!! Form::hidden('IsiSJKir[]', $isisjkirim->IsiSJKir) !!}
              <td>{!! Form::text('Tgl[]', $isisjkirim->S, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::text('Barang[]', $isisjkirim->Barang, array('class' => 'form-control', 'readonly')) !!}</td>
              <td>{!! Form::select('Warehouse[]', $warehouse, $isisjkirim->Warehouse, ['class' => 'form-control']) !!}</td>
              <td>{!! Form::text('QSisaKem[]', $isisjkirim->SumQSisaKemInsert, array('class' => 'form-control', 'readonly')) !!}</td>
              <td><input name="QTertanda[]" type="number" class="form-control" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $isisjkirim->SumQSisaKemInsert }})" value="{{ $isisjkirim->SumQSisaKemInsert }}" required></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        {!! Form::submit('Insert',  array('class' => 'btn btn-success pull-right')) !!}
        <a href="{{route('sjkembali.create', 'id='.$id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
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