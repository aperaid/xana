@extends('layouts.xana.layout')
@section('title')
	Edit SJ Kembali
@stop

@section('content')
{!! Form::model($sjkembali, [
  'method' => 'patch',
  'route' => ['sjkembali.update', $sjkembali->id]
]) !!}
<div class="row">
  <div class="col-xs-12">
    <div class="box box-primary">
      <div class="box-body no-padding">
        <table id="datatables" class="table table-bordered">
					<thead>
            <tr>
              <th>Tanggal Kirim</th>
              <th>Barang</th>
              <th>Warehouse</th>
              <th>Q di Proyek</th>
              <th>Q Pengambilan</th>
            </tr>
					</thead>
					<tbody>
            @foreach($isisjkembalis as $isisjkembali)
            <tr>
              {!! Form::hidden('id[]', $isisjkembali->id) !!}
              {!! Form::hidden('Purchase[]', $isisjkembali->Purchase) !!}
							{!! Form::hidden('IsiSJKem[]', $isisjkembali->IsiSJKem) !!}
              {!! Form::hidden('IsiSJKir[]', $isisjkembali->IsiSJKir) !!}
              {!! Form::hidden('QTerima[]', $isisjkembali->QTerima) !!}
              <td>{!! Form::text('Tgl[]', $isisjkembali->Tgl, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('Barang[]', $isisjkembali->Barang, ['class' => 'form-control', 'autocomplete' => 'off', 'readonly']) !!}</td>
              <td>{!! Form::select('Warehouse[]', ['Kumbang'=>'Kumbang', 'BulakSereh'=>'Bulak Sereh', 'Legok'=>'Legok', 'CitraGarden'=>'Citra Garden'], $isisjkembali->Warehouse, ['class' => 'form-control']) !!}</td>
              <td>{!! Form::text('QSisaKem[]', $isisjkembali->QSisaKem, ['class' => 'form-control', 'readonly']) !!}</td>
              <td><input name="QTertanda[]" type="number" class="form-control" autocomplete="off" onkeyup="this.value = minmax(this.value, 0, {{ $isisjkembali->QSisaKem }})" value="{{ $isisjkembali->SumQTertanda }}" required></td>
						</tr>
						@endforeach
					</tbody>
        </table>
			</div>
      <div class="box-footer">
        <label>Tanggal SJ Kembali</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          {!! Form::text('Tgl2', $sjkembali->Tgl, ['id' => 'Tgl2', 'class' => 'form-control', 'autocomplete' => 'off', 'required']) !!}
        </div>
				<br>
        <div class="box-footer">
          <a href="{{route('sjkembali.show', $sjkembali->id)}}"><button type="button" class="btn btn-default">Cancel</button></a>
          {!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
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
var Min = '{{ $TglMin->S }}';
var Max = '{{ $TglMax->E }}';
$(function() {
  $('#Tgl2').datepicker({
	  format: "dd/mm/yyyy",
	  startDate: Min,
	  endDate: Max,
	  todayHighlight: true,
	  autoclose: true
  }); 
}); 
</script>
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