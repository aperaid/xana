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
            <th>Q Pengambilan</th>
					  <th>Q Terima</th>
            </tr>
					</thead>
					<tbody>
          {{$x = 1}}
					@foreach($isisjkembalis as $isisjkembali)
            <tr>
              {!! Form::hidden('id', $isisjkembali->id) !!}
              {!! Form::hidden('QSisaKem2', $isisjkembali->QTertanda, ['id' => 'QSisaKem2'.$x]) !!}
              {!! Form::hidden('IsiSJKir', $isisjkembali->IsiSJKir) !!}
              {!! Form::hidden('Purchase', $isisjkembali->Purchase) !!}
              {!! Form::hidden('QTerima2', $isisjkembali->QTerima2) !!}
              <td>{!! Form::text('Tgl', $isisjkembali->Tgl, ['class' => 'form-control', 'readonly']) !!}</td>
							<td>{!! Form::text('Barang', $isisjkembali->Barang, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('Warehouse', $isisjkembali->Warehouse, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('QTertanda', $isisjkembali->QTertanda2, ['class' => 'form-control', 'readonly']) !!}</td>
              <td>{!! Form::text('QSisaKem', $isisjkembali->QSisaKem, ['id' => 'QSisaKem'.$x, 'readonly']) !!}</td>
              <td>{!! Form::text('QTerima', $isisjkembali->QTerima2, array('id' => 'QTerima'.$x, 'class' => 'form-control', 'autocomplete' => 'off', 'onkeyup' => 'this.value = minmax(this.value, 0, $isisjkembali->QTertanda2)', 'onkeyup' => 'sisa()', 'required')) !!}</td>
            </tr>
            {{$x++}}
          @endforeach
					</tbody>
				</table>
			</div>
      <!-- box-body -->
      <div class="box-footer">
        <label>Tanggal Selesai Penghitungan</label>
        <div class="input-group">
          <div class="input-group-addon">
            <i class="fa fa-calendar"></i>
          </div>
          {!! Form::text('Tgl', $Tgl->E, ['id' => 'Tgl', 'class' => 'form-control', 'autocomplete' => 'off', 'required']) !!}
				</div>
				<br>
				<a href="{{route('sjkembali.show', $sjkembali->id)}}"><button type="button" class="btn btn-default pull-left">Cancel</button></a>
				{!! Form::submit('Update', array('class' => 'btn btn-info pull-right')) !!}
			</div>
      <!-- box-footer -->
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
  function sisa() {
  for(x = 1; x < 11; x++){
    var txtFirstNumberValue = document.getElementById('hd_editsjkembaliquantity_QSisaKem2'+x).value;
    var txtSecondNumberValue = document.getElementById('tx_editsjkembaliquantity_QTerima'+x).value;
	var result = parseInt(txtFirstNumberValue) - parseInt(txtSecondNumberValue);
	  if (!isNaN(result)) {
		document.getElementById('tx_editsjkembaliquantity_QSisaKem'+x).value = result;
      }
   }
   }
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
<script>
var Min = '{{ $sjkembali->Tgl }}';
$(function() {
  $('#Tgl').datepicker({
	  format: "dd/mm/yyyy",
	  startDate: Min,
	  todayHighlight: true,
	  autoclose: true
  }); 
});
</script>
@stop